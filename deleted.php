

$allInfoList = explode("[", $allInfo);

$serverInfoFrom = $allInfoList[1];
$eachReplication = explode("{", $serverInfoFrom);

$data = [];

$data[] = [
    "hostNameTo" => $hostNameTo,
];

<script>
    function replicationOrganized(){
        $hostNameTo = runCommand("hostname");

        $allInfo = runCommand(sudo() . "samba-tool drs showrepl --json");
        $allInfo = json_decode($allInfo,true);

        $data = [];

        for ($i=0; $i < count($allInfo["repsFrom"]); $i++) {
            $replicationInfo = explode(",", $eachReplication);
        
            if($replicationInfo[0] != "" && $replicationInfo[8] != ""){
                $data[] = [
                    "hostNameTo" => $hostNameTo,
                    "info" => $replicationInfo[0],
                    "hostNameFrom" => $replicationInfo[8],
                ];
            }
        }

        return view('table', [
            "value" => $data,
            "title" => ["HostName To", "Info", "HostName From"],
            "display" => ["hostNameTo", "info", "hostNameFrom"],
        ]);

//        return respond($allInfo["repsFrom"], 200);
    }

    function tab222(){
        var form = new FormData();
        request("{{API('replicationOrganized')}}", form, function(response) {
            $('.replicationTable').html(response).find('table').DataTable({
            bFilter: true,
            "language" : {
                url : "/turkce.json"
            }
        });;
//            message = JSON.parse(response)["message"];
//            $('#tab2').html(message);
        }, function(error) {
            $('#tab2').html("Hata oluştu");
        });
    }
</script>