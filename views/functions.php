<?php  
  
    function currentHostName() {
        return respond(strtoupper(runCommand("hostname")));
    }

    function replication(){
        return respond(runCommand(sudo() . "samba-tool drs showrepl"),200);
    }

    function replicationOrganized(){
        $allInfo = runCommand(sudo() . "samba-tool drs showrepl --json");
        $allInfo = json_decode($allInfo,true);

        $data = [];

        for ($i=0; $i < count($allInfo["repsFrom"]); $i++) {
            $pureHostName = str_replace("Default-First-Site-Name\\", "", $allInfo["repsFrom"][$i]["DSA"]);
            $data[] = [
                "destinationHostName" => strtoupper(runCommand("hostname")),
                "info" => $allInfo["repsFrom"][$i]["NC dn"],
                "sourceHostName" => $pureHostName,
                "lastUpdateTime" => $allInfo["repsFrom"][$i]["last success"],
                "rowNumber" => $i
            ];
        }

        return view('table', [
            "value" => $data,
            "title" => ["Source Host Name", "NC-dn Info", "Destination Host Name", "*hidden*", "*hidden*"],
            "display" => ["sourceHostName", "info", "destinationHostName", "lastUpdateTime:lastUpdateTime", "rowNumber:rowNumber"],
            "onclick" => "test",
            "menu" => [

                "Update Replication" => [
                    "target" => "updateReplication",
                    "icon" => "fa-recycle"
                ],

                "Last Update Time" => [
                    "target" => "showUpdateTime",
                    "icon" => "fa-clock"
                ],
            ],
        ]);   
    }

    function updateReplication(){ 
        $incomingHostName = request('inHost');
        $outgoingHostName = request('outHost');
        $info = request('info');
        return respond(runCommand(sudo() . 'samba-tool drs replicate ' . $incomingHostName . ' ' . $outgoingHostName . ' ' . $info), 200);
    }

    function showUpdateTime(){
        $allInfo = runCommand(sudo() . "samba-tool drs showrepl --json");
        $allInfo = json_decode($allInfo,true);
        $rNumber = request("rowNumber");
        $lastUpdateTime = $allInfo["repsFrom"][$rNumber]["last success"];
        return respond($lastUpdateTime, 200);        
    }

    function createReplicationWithModal() {
        $allInfo = runCommand(sudo() . "samba-tool drs showrepl --json");
        $allInfo = json_decode($allInfo,true);

        $compaundData = request("compaundData");
        $compaundDataList = explode("-", $compaundData);
        $ncDnInfo = $compaundDataList[0];

        $destinationHostName = strtoupper(runCommand("hostname"));
        $sourceHostName = str_replace("Default-First-Site-Name\\", "", $allInfo["repsFrom"][$compaundDataList[1]]["DSA"]);

        return respond(runCommand(sudo() . 'samba-tool drs replicate ' . $destinationHostName . ' ' . $sourceHostName . ' ' . $ncDnInfo), 200);
    }

    function test1() {
        $var = runCommand('echo selam');
        return respond($var, 200);
    }
?>