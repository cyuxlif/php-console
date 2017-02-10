
var serverSocketId;

/**
 *创建监听
 */
chrome.sockets.tcpServer.create({}, function(createInfo) {
    if(serverSocketId > 0){
        chrome.sockets.tcpServer.disconnect(serverSocketId)
        chrome.sockets.tcpServer.close(serverSocketId)
    }
    serverSocketId = createInfo.socketId;
    chrome.sockets.tcpServer.listen(serverSocketId, '0.0.0.0', 3003, function(resultCode) {
        if (resultCode < 0) {
            console.error("Error listening:" + chrome.runtime.lastError.message);
        }else{
            console.info("listening on 0.0.0.0:3003, serverSocketId: %d", serverSocketId);
        }
    });
});


chrome.sockets.tcpServer.onAccept.addListener(function(info) {
    if (info.socketId === serverSocketId) {
        chrome.sockets.tcp.setPaused(info.clientSocketId, false);
    }
});

/**
*接收php客户端传过来的数据 并打印
*/
var requestText = '';
chrome.sockets.tcp.onReceive.addListener(function(info) {
    requestText += ab2str(info.data);

    var socketId = info.socketId;
    if(requestText.substr(-4, 4) == '|EOM'){
        requestText = requestText.substring(0, requestText.length - 4)
        JSON.parse(requestText.toString()).forEach(function(v, i){
            console[v.type](v.msg);
        });
        chrome.sockets.tcp.disconnect(socketId);
        chrome.sockets.tcp.close(socketId);
        requestText = '';
    }
});

/**
 *接收数据错误
 */
chrome.sockets.tcp.onReceiveError.addListener(function(info) {
    if(info.resultCode == -100){
        //客户端关闭了socket
        console.log("\n");
        requestText = '';
    }else{
        console.log("Error: ", info);
    }
});

/**
 * @param text
 * @returns {ArrayBuffer}
 */
function str2ab(text) {
    var typedArray = new Uint8Array(text.length);

    for (var i = 0; i < typedArray.length; i++) {
        typedArray[i] = text.charCodeAt(i);
    }

    return typedArray.buffer;
}

/**
 * @param arrayBuffer
 * @returns {string}
 */
function ab2str(arrayBuffer) {
    var typedArray = new Uint8Array(arrayBuffer);
    var text = '';

    for (var i = 0; i < typedArray.length; i++) {
        text += String.fromCharCode(typedArray[i]);
    }

    return text;
}


