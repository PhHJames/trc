require("./config/config.js");

const TronWeb = require('tronweb')
const HttpProvider = TronWeb.providers.HttpProvider;
const fullNode = new HttpProvider("https://api.trongrid.io");
const solidityNode = new HttpProvider("https://api.trongrid.io");
const eventServer = new HttpProvider("https://api.trongrid.io");


/*const fullNode = new HttpProvider("https://api.shasta.trongrid.io");
const solidityNode = new HttpProvider("https://api.shasta.trongrid.io");
const eventServer = new HttpProvider("https://api.shasta.trongrid.io");

*/
const  privateKey = "";
//const tronWeb = new TronWeb(fullNode,solidityNode,eventServer,privateKey);

const tronWeb = new TronWeb(fullNode,solidityNode,eventServer,privateKey);
tronWeb.setHeader({"TRON-PRO-API-KEY": tronWebAPiKey});

console.log("生成的本地地址如下：")
 tronWeb.createAccount().then( function(address){
    //console.log(address)
     console.log("地址：" + address.address.base58 )
     console.log("私钥：" + address.privateKey )
})




