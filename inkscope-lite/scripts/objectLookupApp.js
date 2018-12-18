/**
 * Created by Alain Dechorgnat on 1/18/14.
 */
// angular stuff
// create module for custom directives
var ObjectLookupApp = angular.module('ObjectLookupApp', ['D3Directives','InkscopeCommons'])
    .filter('bytes', funcBytesFilter)
    .filter('duration',funcDurationFilter);

ObjectLookupApp.controller("ObjectLookupCtrl", function ($rootScope, $scope, $http) {
    $scope.pool = "";

    // start refresh when fsid is available
    var waitForFsid = function ($rootScope, $http,$scope){
        typeof $rootScope.fsid !== "undefined"? startRefresh($rootScope, $http,$scope) : setTimeout(function () {waitForFsid($rootScope, $http,$scope)}, 1000);
        function startRefresh($rootScope, $http,$scope){
            getOsdInfo();
            setInterval(function () {getOsdInfo()},10*1000);
        }
    }
    waitForFsid($rootScope, $http,$scope);


    getPoolsInfo();

    getObjectInfo();
    setInterval(function () {getObjectInfo()},5*1000);

    function getPoolsInfo() {
        $http({method: "get", url: cephRestApiURL + "df.json"}).
            success(function (data, status) {
                $scope.status = status;
                $scope.date = new Date();
                $scope.pools =  data.output.pools;
            }).
            error(function (data, status, headers) {
                //alert("refresh pools failed with status "+status);
                $scope.status = status;
                $scope.date = new Date();
                $scope.pools =  [];
            });
    }

    function getOsdInfo(){
        $http({method: "get", url: cephRestApiURL + "osd/dump.json"}).

            success(function (data, status) {
                data = data.output.osds;

                // adaptation to mongodb inkscope format
                for ( var i=0; i<data.length;i++){
                    data[i].stat = {};
                    data[i].stat.in =(data[i].in == 1);
                    data[i].stat.up =(data[i].up == 1);
                    data[i].id = data[i].osd ;
                }

                data[-1]={};
                data[-1].id=-1;
                data[2147483647]={};
                data[2147483647].id=-1;

                $rootScope.data = data;
                $scope.$apply();
            }).
            error(function (data, status) {
                $rootScope.status = status;
                $rootScope.data = {};
            });
    }


    function getObjectInfo() {
        $rootScope.date = new Date();
        if ($scope.pool+"" =="undefined" || $scope.objectId+"" =="undefined") return;

        console.log(cephRestApiURL + "osd/map?pool="+ $scope.pool +"&object="+ $scope.objectId );
        $http({method: "get", url: cephRestApiURL + "osd/map.json?pool="+$scope.pool+"&object="+$scope.objectId}).

            success(function (data, status) {
                $scope.data = data.output;

                // new format in Giant
                if (typeof $scope.data.acting ==="string")
                    $scope.data.acting = JSON.parse($scope.data.acting);
                $scope.acting = $scope.data.acting;

                // new format in Giant
                if (typeof $scope.data.up ==="string")
                    $scope.data.up = JSON.parse($scope.data.up);


                $scope.acting_message ="";
                $scope.up_message = "";
                if ($scope.data.acting.indexOf("-1")>0 || $scope.data.acting.indexOf("2147483647")>0)  $scope.acting_message = " - incomplete pg"
                if ($scope.data.up.indexOf("-1")>0 || $scope.data.up.indexOf("2147483647")>0)  $scope.up_message = " - incomplete pg"

                /*$scope.data.acting = $scope.data.acting.replace(new RegExp("2147483647", 'g'),"-1");
                $scope.data.up = $scope.data.up.replace(new RegExp("2147483647", 'g'),"-1");
                */
                $scope.data.acting += $scope.acting_message;
                $scope.data.up += $scope.up_message;
            }).
            error(function (data, status) {
                $rootScope.status = status;
                $scope.data = {"pgid":"not found","acting":"","up":""};
            });

    }

    $rootScope.osdClass = function (osdin,osdup){
        var osdclass = (osdin == true) ? "osd_in " : "osd_out ";
        osdclass += (osdup == true) ? "osd_up" : "osd_down";
        return osdclass;

    }

    $rootScope.osdClassForId = function (osdid){
        var osdin = "out";
        var osdup = "down";
        if (osdid>=0){
            osdin = $rootScope.getOsd(osdid).stat.in;
            osdup = $rootScope.getOsd(osdid).stat.up;
        }
        return $rootScope.osdClass(osdin,osdup);
    }

    $rootScope.osdState = function (osdin,osdup){
        var osdstate = (osdin == true) ? "in / " : "out / ";
        osdstate += (osdup == true) ? "up" : "down";
        return osdstate;

    }

    $rootScope.prettyPrint = function( object){
        return object.toString();
    }

    $rootScope.prettyPrintKey = function( key){
        return key.replace(new RegExp( "_", "g" )," ")
    }


    $rootScope.osdSelect = function (osd) {
        $rootScope.osd = osd;
        $rootScope.selectedOsd = osd.node._id;
    }

    $rootScope.getOsd = function (osd) {
        console.log("search for osd:"+osd);
        for (var i=0 ;i<$rootScope.data.length;i++){
            if ($rootScope.data[i].osd+"" == osd+"") {
                console.log("osd found "+JSON.stringify($rootScope.data[i]));
                return $rootScope.data[i];
            }
        }
        console.log("osd not found");
    }


});