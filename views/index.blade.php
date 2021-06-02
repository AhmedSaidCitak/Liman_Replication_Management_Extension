<h1>{{ __('Replication Managemant') }}</h1>

@component('modal-component',[
    "id" => "ReplicationModal",
    "title" => "Replication",
    "footer" => [
        "text" => "Replicate",
        "class" => "btn-success",
        "onclick" => "selectReplication()"
    ]
])
@include('inputs', [
    "inputs" => [
            "DC/CN:newType" => [
                "ForestDnsZones" => "DC=ForestDnsZones,DC=deneme,DC=lab-0",
                "Base" => "DC=deneme,DC=lab-1",
                "Schema" => "CN=Schema,CN=Configuration,DC=deneme,DC=lab-2",
                "DomainDnsZones" => "DC=DomainDnsZones,DC=deneme,DC=lab-3",
                "Configuration" => "CN=Configuration,DC=deneme,DC=lab-4",
            ],
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
        <a class="nav-link active"  onclick="currentHostName()" href="#tab1" data-toggle="tab">HostName</a>
    </li>
    <li class="nav-item">
        <a class="nav-link "  onclick="replicationInfo()" href="#tab2" data-toggle="tab">Replication Info</a>
    </li>
    <li class="nav-item">
        <a class="nav-link"  onclick="showCurrentServer()" href="#tab3" data-toggle="tab">Current Server Name</a>
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
        currentHostName();
    }

    function currentHostName(){
        var form = new FormData();
        request("{{API('currentHostName')}}", form, function(response) {
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
        showSwal('{{__("Yükleniyor...")}}','info',2000);
        var form = new FormData();

        let inHost = line.querySelector("#destinationHostName").innerHTML;
        let info = line.querySelector("#info").innerHTML;
        let outHost = line.querySelector("#sourceHostName").innerHTML;

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
        let rowNumber = line.querySelector("#rowNumber").innerHTML; 

        form.append("lastUpdateTime", lastUpdateTime);
        form.append("rowNumber", rowNumber);

        request(API('showUpdateTime'), form, function(response) {
            message = JSON.parse(response)["message"];
            console.log(message);
            showSwal(message, 'info', 3000);
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000);
        });
    }

    function showReplicationModal() {
        $('#ReplicationModal').modal("show");
    }

    function selectReplication(line) {
        showSwal('{{__("Yükleniyor...")}}','info',2000);
        var form = new FormData();

        let compaundData = $('#ReplicationModal').find('select[name=newType]').val();

        form.append("compaundData", compaundData);

        request(API('createReplicationWithModal'), form, function(response) {
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

    function showCurrentServer(flag){
        $('#currentServerModal').modal("show");
        var serverName = document.getElementById("tab1").innerText;
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