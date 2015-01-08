/**
 * Created by zerilliworks on 7/28/14.
 */

var DemoApp = angular.module('redisDemo', ['ui.bootstrap', 'ui.ace'])
    //
    //  QueryController                 ///////////////////////////////////////
    //
    .controller('QueryController', ['$scope', function ($scope) {
        $scope.results = [];
        $scope.commandHistory = [];
        $scope.historyCursor = 0;
        localforage.getItem('commandHistory', function (hist) {
            $scope.commandHistory = hist || [];
            $scope.$apply();
        });
        $scope.lastQuery = {query: "", status: "default", message: ""};
        $scope.commands = [
            "APPEND",
            "AUTH",
            "BGREWRITEAOF",
            "BGSAVE",
            "BITCOUNT",
            "BITOP",
            "BITPOS",
            "BLPOP",
            "BRPOP",
            "BRPOPLPUSH",
            "CLIENT KILL",
            "CLIENT LIST",
            "CLIENT GETNAME",
            "CLIENT PAUSE",
            "CLIENT SETNAME",
            "CLUSTER SLOTS",
            "COMMAND",
            "COMMAND COUNT",
            "COMMAND GETKEYS",
            "COMMAND INFO",
            "CONFIG GET",
            "CONFIG REWRITE",
            "CONFIG SET",
            "CONFIG RESETSTAT",
            "DBSIZE",
            "DEBUG OBJECT",
            "DEBUG SEGFAULT",
            "DECR",
            "DECRBY",
            "DEL",
            "DISCARD",
            "DUMP",
            "ECHO",
            "EVAL",
            "EVALSHA",
            "EXEC",
            "EXISTS",
            "EXPIRE",
            "EXPIREAT",
            "FLUSHALL",
            "FLUSHDB",
            "GET",
            "GETBIT",
            "GETRANGE",
            "GETSET",
            "HDEL",
            "HEXISTS",
            "HGET",
            "HGETALL",
            "HINCRBY",
            "HINCRBYFLOAT",
            "HKEYS",
            "HLEN",
            "HMGET",
            "HMSET",
            "HSET",
            "HSETNX",
            "HVALS",
            "INCR",
            "INCRBY",
            "INCRBYFLOAT",
            "INFO",
            "KEYS",
            "LASTSAVE",
            "LINDEX",
            "LINSERT",
            "LLEN",
            "LPOP",
            "LPUSH",
            "LPUSHX",
            "LRANGE",
            "LREM",
            "LSET",
            "LTRIM",
            "MGET",
            "MIGRATE",
            "MONITOR",
            "MOVE",
            "MSET",
            "MSETNX",
            "MULTI",
            "OBJECT",
            "PERSIST",
            "PEXPIRE",
            "PEXPIREAT",
            "PFADD",
            "PFCOUNT",
            "PFMERGE",
            "PING",
            "PSETEX",
            "PSUBSCRIBE",
            "PUBSUB",
            "PTTL",
            "PUBLISH",
            "PUNSUBSCRIBE",
            "QUIT",
            "RANDOMKEY",
            "RENAME",
            "RENAMENX",
            "RESTORE",
            "ROLE",
            "RPOP",
            "RPOPLPUSH",
            "RPUSH",
            "RPUSHX",
            "SADD",
            "SAVE",
            "SCARD",
            "SCRIPT EXISTS",
            "SCRIPT FLUSH",
            "SCRIPT KILL",
            "SCRIPT LOAD",
            "SDIFF",
            "SDIFFSTORE",
            "SELECT",
            "SET",
            "SETBIT",
            "SETEX",
            "SETNX",
            "SETRANGE",
            "SHUTDOWN",
            "SINTER",
            "SINTERSTORE",
            "SISMEMBER",
            "SLAVEOF",
            "SLOWLOG",
            "SMEMBERS",
            "SMOVE",
            "SORT",
            "SPOP",
            "SRANDMEMBER",
            "SREM",
            "STRLEN",
            "SUBSCRIBE",
            "SUNION",
            "SUNIONSTORE",
            "SYNC",
            "TIME",
            "TTL",
            "TYPE",
            "UNSUBSCRIBE",
            "UNWATCH",
            "WATCH",
            "ZADD",
            "ZCARD",
            "ZCOUNT",
            "ZINCRBY",
            "ZINTERSTORE",
            "ZLEXCOUNT",
            "ZRANGE",
            "ZRANGEBYLEX",
            "ZRANGEBYSCORE",
            "ZRANK",
            "ZREM",
            "ZREMRANGEBYLEX",
            "ZREMRANGEBYRANK",
            "ZREMRANGEBYSCORE",
            "ZREVRANGE",
            "ZREVRANGEBYSCORE",
            "ZREVRANK",
            "ZSCORE",
            "ZUNIONSTORE",
            "SCAN",
            "SSCAN",
            "HSCAN",
            "ZSCAN"
        ];

        $scope.queryString = "";
        $scope.useScript = false;
        $scope.scriptBody = "";

        jQuery("input[name='query']").keydown(function (e) {
            switch (e.which) {
                case 38:
                    // up
                    $scope.fillQueryBox($scope.commandHistory[$scope.historyCursor]);
                    jQuery("input[name='query']").val($scope.commandHistory[$scope.historyCursor]);
                    $scope.historyCursor++;
                    if ($scope.historyCursor >= $scope.commandHistory.length) {
                        $scope.historyCursor = 0;
                    }
                    break;
                case 40:
                    // down
                    $scope.fillQueryBox($scope.commandHistory[$scope.historyCursor]);
                    jQuery("input[name='query']").val($scope.commandHistory[$scope.historyCursor]);
                    $scope.historyCursor--;
                    if ($scope.historyCursor < 0) {
                        $scope.historyCursor = $scope.commandHistory.length - 1;
                    }
                    break;
            }
        });

        $scope.submitQuery = function () {
            console.log('Querying');

            if ($scope.useScript) {
                $scope.lastQuery.query = "EVAL " + $scope.scriptBody.substr(0, 40) + "...";
                $scope.commandHistory.unshift("EVAL " + $scope.scriptBody.substr(0, 20) + "...");
            } else {
                $scope.lastQuery.query = $scope.queryString;
                $scope.commandHistory.unshift($scope.queryString);
            }

            $scope.lastQuery.status = "loading";
            jQuery("#submit-query-button").button('loading');

            localforage.setItem('commandHistory', $scope.commandHistory);

            jQuery.post('/query', {query: $scope.queryString, script: (function () {
                if ($scope.useScript) {
                    return $scope.scriptBody;
                } else {
                    return null
                }
            }())}, function (data, textStatus, jqXHR) {
                console.log("Status: " + textStatus);
                console.log("Data: " + data);
                $scope.lastQuery.status = "default";
                $scope.results.unshift({query: $scope.queryString, data: jQuery.parseJSON(data)});
                $scope.queryString = "";
                console.debug($scope.results);
                $scope.$apply();
            })
                .fail(function (data) {
                    console.log("Query failed: " + data.responseText);
                    $scope.lastQuery.status = "error";
                    $scope.lastQuery.message = data.responseText;
                    $scope.$apply();
                })
                .always(function() {
                    jQuery("#submit-query-button").button('reset');
                });
            $scope.historyCursor = 0;
        };
        $scope.isNested = function (value) {
            return typeof value == "object";
        };
        $scope.fillQueryBox = function (value) {
            $scope.queryString = value;
            jQuery("input[name='query']").focus();
        };
        $scope.clearHistory = function () {
            $scope.commandHistory = [];
            localforage.removeItem('commandHistory');
        };

        $scope.toggleScripter = function () {
            $scope.useScript = !$scope.useScript;
        };
    }])

    //
    //  RoomController                 ///////////////////////////////////////
    //

    .controller('RoomController', ['$scope', function ($scope) {
        $scope.rooms = [];


        $scope.createRoom = function (name, host) {
            $BrainSocket.message('app.newroom', {name: name, host: host});
        };

        $scope.listRooms = function () {

        };

        $scope.detectRoom = function (name, host) {

        };

        $scope.removeRoom = function (roomName) {

        };

    }]);

//var $BrainSocket = new BrainSocket(
//    new WebSocket('ws://localhost:8080'),
//    new BrainSocketPubSub()
//);
//
//$BrainSocket.event.listen('app.newroom',function(room){
//    var host = room.host;
//    var roomName = room.name;
//
//    DemoApp.RoomController.detectRoom(roomName, host);
//});
//
//$BrainSocket.event.listen('app.killroom',function(data){
//    DemoApp.RoomController.removeRoom(roomName);
//});
//
//$BrainSocket.event.listen('app.joinroom',function(data){
//    console.log('An app error message was sent');
//    console.log(data);
//});
//
//$BrainSocket.event.listen('app.leaveroom',function(data){
//    console.log('An app error message was sent');
//    console.log(data);
//});
//
//$BrainSocket.event.listen('app.newresult',function(data){
//    console.log('An app error message was sent');
//    console.log(data);
//});