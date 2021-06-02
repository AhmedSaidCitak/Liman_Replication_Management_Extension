<h1>{{ __('Replication Managemant') }}</h1>

@component('modal-component',[
    "id" => "ReplicationModal",
    "title" => "Replication",
    "footer" => [
        "text" => "Replikasyon Oluştur",
        "class" => "btn-success",
        "onclick" => "selectReplication()"
    ]
])
@include('inputs', [
    "inputs" => [
        "DC/CN Name" => "dName:text:DC/CN Name",
    ]
])
@endcomponent

@component('modal-component',[
    "id" => "currentServerModal",
    "title" => "Click \"show\" button to see your server name",
    "footer" => [
        "text" => "Show",
        "class" => "btn-success",
        "onclick" => "showCurrentServer()"
    ]
])
@endcomponent

<ul class="nav nav-tabs" role="tablist" style="margin-bottom: 15px;">
    <li class="nav-item">
        <a class="nav-link active"  onclick="tab1()" href="#tab1" data-toggle="tab">HostName</a>
    </li>
    <li class="nav-item">
        <a class="nav-link "  onclick="replicationInfo()" href="#tab2" data-toggle="tab">Replication Info</a>
    </li>
    <li class="nav-item">
        <a class="nav-link"  onclick="showCurrentServer()" href="#tab1" data-toggle="tab">Current Server Name</a>
    </li>
</ul>

<div id="replicationButton" class="tab-pane">
        <button class="btn btn-success mb-2" id="replButton" onclick="showReplicationModal()" type="button">Create Replication</button>
</div>

<div class="tab-content">
    <div id="tab1" class="tab-pane active">
    </div>

    <div id="tab2" class="tab-pane">
        <div id="replicationPrintArea">
        <div class="table-responsive replicationTable" id="replicationTable"></div> 
    </div>
</div>
</div>

<script>
    if(location.hash === ""){
        tab1();
    }

    function tab1(){
        var form = new FormData();
        request("{{API('tab1')}}", form, function(response) {
            message = JSON.parse(response)["message"];
            $('#tab1').html(message);
        }, function(error) {
            $('#tab1').html("Hata oluştu");
        });
    }

    function replicationInfo(){
        showSwal('{{__("Yükleniyor...")}}','info',2000);
        var form = new FormData();

        request(API('replicationOrganized'), form, function(response) {
            $('.replicationTable').html(response).find('table').DataTable({
            bFilter: true,
            "language" : {
                url : "/turkce.json"
            }
            });;
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000);
        });

    }

    function updateReplication(line) {
        var form = new FormData();

        let inHost = line.querySelector("#hostNameTo").innerHTML;
        let info = line.querySelector("#info").innerHTML;
        let outHost = line.querySelector("#hostNameFrom").innerHTML;

        form.append("inHost", inHost);
        form.append("info", info);
        form.append("outHost", outHost);

        request(API('updateReplication'), form, function(response) {
            message = JSON.parse(response)["message"];
            showSwal(message, 'success', 3000);
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000);
        });
    }

    function showUpdateTime(line) {
        var form = new FormData();

        let lastUpdateTime = line.querySelector("#lastUpdateTime").innerHTML;   

        form.append("lastUpdateTime", lastUpdateTime);

        request(API('showUpdateTime'), form, function(response) {
            message = JSON.parse(response)["message"];
            console.log(message);
            showSwal(message, 'success', 3000);
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000);
        });
    }

    function showReplicationModal() {
        $('#ReplicationModal').modal("show");
    }

    function selectReplication(line) {
        var form = new FormData();
        let dName = $('#ReplicationModal').find('input[name=dName]').val();
        form.append("dName",dName);

        request(API('createBoundWithModal'), form, function(response) {
            message = JSON.parse(response)["message"];
            showSwal(message, 'success', 3000);
            $('#createFileModal').modal("hide");
        }, function(error) {
            showSwal(error.message, 'error', 3000);
        });
    }
    
    // Deneme Fonksiyonu
    function test() {
        var form = new FormData();
        request(API('test1'), form, function(response) {
            message = JSON.parse(response)["message"];
            showSwal(message, 'success', 3000);
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000);
        });
    }

    function showCurrentServer(){
        $('#currentServerModal').modal("show");
        var serverName = document.getElementById("tab1").innerText;
        if(serverName)
            $('#currentServerModal h4.modal-title').html(`Current Server: (${serverName})`);
    }

    getHostname();
    function getHostname(){
        showSwal('{{__("Yükleniyor...")}}', 'info');
        let data = new FormData();
        request("{{API("get_hostname")}}", data, function(response){
            response = JSON.parse(response);
            $('#hostname').text(response.message);
            Swal.close();
            $('#setHostnameModal').modal('hide')
        }, function(response){
            response = JSON.parse(response);
            showSwal(response.message, 'error');
        });
    }
</script>



function createBoundWithModal() {
        $dName = request('dName');
        if($dName == "ForestDnsZones") {
            runCommand(sudo() . 'samba-tool drs replicate SUNUCU2 SUNUCU3 DC=ForestDnsZones,DC=deneme,DC=lab');
            return respond("Replikasyon basariyla gerceklestirildi", 200);
        } 
        elseif($dName == "base") {
            runCommand(sudo() . 'samba-tool drs replicate SUNUCU2 SUNUCU3 DC=deneme,DC=lab');
            return respond("Replikasyon basariyla gerceklestirildi", 200);
        }
        elseif($dName == "Schema") {
            runCommand(sudo() . 'samba-tool drs replicate SUNUCU2 SUNUCU3 CN=Schema,CN=Configuration,DC=deneme,DC=lab');
            return respond("Replikasyon basariyla gerceklestirildi", 200);
        }
        elseif($dName == "DomainDnsZones") {
            runCommand(sudo() . 'samba-tool drs replicate SUNUCU2 SUNUCU3 DC=DomainDnsZones,DC=deneme,DC=lab');
            return respond("Replikasyon basariyla gerceklestirildi", 200);
        }
        elseif($dName == "Configuration") {
            runCommand(sudo() . 'samba-tool drs replicate SUNUCU2 SUNUCU3 CN=Configuration,DC=deneme,DC=lab');
            return respond("Replikasyon basariyla gerceklestirildi", 200);
        }
        else{
            return respond("Gecerli Bir Domain Secin!", 400);
        }
    }