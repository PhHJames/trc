/* 
* @Author: Awe
* @Date:   2021-02-02 01:37:02
* @Last Modified by:   Awe
* @Last Modified time: 2021-02-02 02:34:40
* @desc 轮训trx的交易  监控trx 最新区块的交易记录
*/
var fs = require('fs'); // 引入fs模块
require("./config/config.js");
const superagent = require('superagent')
//var fs = require('fs'); // 引入fs模块
const TronWeb = require('tronweb')
const HttpProvider = TronWeb.providers.HttpProvider;
const CommonModules = require ('./module/common.js') 

//线上环境
const fullNode = new HttpProvider("https://api.trongrid.io");
const solidityNode = new HttpProvider("https://api.trongrid.io");
const eventServer = new HttpProvider("https://api.trongrid.io");
const tronWeb = new TronWeb(fullNode,solidityNode,eventServer);
tronWeb.setHeader({"TRON-PRO-API-KEY": tronWebAPiKey});


const redis = require('redis');

var redisClient = redis.createClient(redis_port,redis_host);
if(redis_pass){
    redisClient.auth(redis_pass); 
}
redisClient.select(15);
redisClient.on('error',function(error){
    console.log("redis: " + error);
});

async function sIsMember(key , value ){
    return new Promise( (resolve) => {
        redisClient.sismember(key,value ,function(err, res){
            return resolve(res);
        });
    });
}


async function resJob(){
    try{
        var result = await  tronWeb.trx.getCurrentBlock()

        let str = JSON.stringify(result)

        let blockID = result.blockID
        let transactions = result.transactions
        if(transactions.length > 0 ){
            for(var it in transactions ){
                let txID = transactions[it].txID
                let raw_data = transactions[it].raw_data
                let valueData = raw_data['contract'][0]['parameter']['value']
                let type = raw_data['contract'][0]['type']
                //from = tronWeb.address.fromHex(from)
                let owner_address = valueData['owner_address']
                owner_address = tronWeb.address.fromHex(owner_address)
               
                let to_address = valueData['to_address']
                to_address = tronWeb.address.fromHex(to_address)
                let amount = valueData['amount']
                amount = amount / 1000000
                var j_data = JSON.stringify(transactions[it])
                let str  = "owner_address"+owner_address + " , to_address  :" +to_address +",txID : "+txID + ",amount:"+amount + ",type:"+type + " , 原始数据："+j_data ;
                //fs.writeFileSync("./123.txt",str + "\r\n",{flag : 'a'} );
               
                var req = {
                    owner_address : owner_address ,
                    to_address :to_address ,
                    txID :txID ,
                    amount : amount ,
                    extra : transactions[it]
                }
                let redis_key = "system_address"

                let from_k_v = await sIsMember(redis_key , owner_address)
                let to_k_v = await sIsMember(redis_key , to_address)
                if(from_k_v != 1 && to_k_v != 1 ){
                    CommonModules.Common.consoleLog("from地址："+owner_address+",to地址:"+to_address+",不是系统的 ， 交易id："+txID+",无需处理。。。")
                    continue;
                }
                let resp = await sendReq(req)
                //redisClient.set(redis_key, 1);
                //redisClient.expire(redis_key, 300);
                console.log(resp)
                //console.log(str)
            }
        }
    }catch(err){
        console.log("err:" + err )
    }
}

async function sendReq( postData ){
    var url = web_api_domain + "/Api_Rsync/trx_trans";
    return new Promise(( resolve, reject ) => {
        try{
            superagent.post(url)
            .accept('application/json')
            .timeout(5000)
            .set('Content-Type', 'application/json')
            .send(postData)
            .end(function(err, resp) {
                if (err) {
                    reject( err )
                    return ;
                }
                //console.log(typeof resp.statusCode)
                let statusCode = ""
                if(resp.hasOwnProperty("statusCode") ){
                    statusCode = resp.statusCode
                }
                let status = ""
                if( resp.hasOwnProperty("status")  ){
                    status = resp.status
                }
                //let text = resp.body.text || ""
                //let repCode = resp.body.repCode || ""
                if(statusCode == 200 && status == 200 ){
                    let body = resp.text || {}
                    resolve( body )
                }else{
                    reject( resp )
                }
            })
        }catch(err){
            reject( err )
        }
        
    })
}

resJob()

setInterval(resJob, 2000);//循环执行
