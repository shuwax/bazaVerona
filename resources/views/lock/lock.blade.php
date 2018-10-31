@extends('main')
@section('style')
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          type="text/css"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css" type="text/css"/>
@endsection

@section('content')
    <div class="page-header">
        <h1>Blokowanie</h1>
    </div>
    <ul id="lockingNav" class="nav nav-tabs" style="margin-bottom: 1em">
        <li role="presentation" class="active"><a href="#lockSingle">Pojedynczo</a></li>
        <li role="presentation"><a href="#lockMulti">Paczka</a></li>
    </ul>
    <div id="lockSinglePanel" class="panel panel-default lockPanel">
        <div class="panel-heading">
            Panel blokowania pojedynczego numeru
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="alertBoxSingle">

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1">Nr telefonu</span>
                        <input id="lockInput" type="number" min="0" class="form-control" placeholder="Nr telefonu"
                               aria-describedby="basic-addon1">
                    </div>
                </div>
                <div class="col-lg-6">
                    <button id="lock" class="btn btn-default btn-block">
                        Zablokuj
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div id="lockMultiPanel" class="panel panel-default lockPanel" hidden="hidden">
        <div class="panel-heading">
            Panel blokowania paczki numerów
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="alertBoxMulti">

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <input style="padding-bottom: 3em" id="lockFileInput" type="file" class="form-control" placeholder="Plik .csv">
                </div>
                <div class="col-lg-6">
                    <button id="lockMulti" class="btn btn-primary btn-block" disabled>
                        Wgraj plik
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div id="lockMultiPanelSecond" class="panel panel-default lockPanel" hidden="hidden">
        <div class="panel-heading">
            Panel blokowania paczki numerów
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="alertBoxMultiSecond">

                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr>
    <div class="panel panel-default">
        <div class="panel-heading">
            Historia blokowania
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-lg-4">
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1">Baza</span>
                        <input id="baseInput" type="number" min="0" class="form-control" placeholder="Baza"
                               aria-describedby="basic-addon1">
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1">Nr telefonu</span>
                        <input id="telInput" type="number" min="0" class="form-control" placeholder="Nr telefonu"
                               aria-describedby="basic-addon1">
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1">Data zablokowania</span>
                        <input id="dateInput" type="date"  class="form-control" placeholder="Data zablokowania"
                               aria-describedby="basic-addon1">
                    </div>
                </div>

            </div>
            <div class="row" style="margin-top: 1em">
                <div class="col-lg-12">
                    <table id="datatable" class="table stripe row-border">
                        <thead>
                        <tr>
                            <th>Baza</th>
                            <th>Telefon</th>
                            <th>Data zablokowania</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script type="text/javascript" charset="utf8"
            src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" charset="utf8"
            src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.28.11/sweetalert2.all.js"></script>
    <script>
        /** --------------------------------- VARIABLES --------------------------------- **/
        let alertBoxSingle = $('.alertBoxSingle');
        let alertBoxMulti = $('.alertBoxMulti');
        let lockFileInput = $('#lockFileInput');
        let navTabs = $('#lockingNav');
        let lockSinglePanel = $('#lockSinglePanel');
        let lockMultiPanel = $('#lockMultiPanel');
        let lockMultiPanelSecond = $('#lockMultiPanelSecond');
        let lockMultiButton = $('#lockMulti');
        let lockMultiSecondButton = $('#lockMultiSecond');
        let newRecordsLocked = 0;
        let oldRecordsLocked = 0;

        /** --------------------------------- DATATABLE --------------------------------- **/
        let datatable = $("#datatable").DataTable({
            autoWidth: true,
            processing: true,
            serverSide: true,
            scrollY: '40vh',
            scrollCollapse: true,
            order: [[2, 'desc']],
            ajax: {
                url: "{{ route('api.datatableLockAjax') }}",
                type: 'POST',
                data: function(d) {
                    d.id_baza = $('#baseInput').val();
                    d.telefon = $('#telInput').val();
                    d.created_at = $('#dateInput').val();
                },
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
            },
            language: {
                "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Polish.json"
            },
            columns: [
                {data: 'id_baza'},
                {data: 'telefon'},
                {data: 'created_at'}
            ]
        });

        /** --------------------------------- EVENT LISTENERS --------------------------------- **/

        $('#baseInput').change(onInputChange);
        $('#telInput').change(onInputChange);
        $('#dateInput').change(onInputChange);

        function onInputChange(e){
            datatable.ajax.reload();
        }

        $('#lock').click((e) => {
            if($('#lockInput').val() !== ''){
            swal({
                title: 'Czy na pewno?',
                type: 'warning',
                html: 'Czy na pewno chcesz zablokować numer <strong>'+$('#lockInput').val()+'</strong>',
                showCloseButton: true,
                showCancelButton: true,
                confirmButtonText: 'Tak, zablokuj!'
            }).then(function (result) {
                if(result.value){
                    $.ajax({
                        url: "{{ route('api.lockPostAjax') }}",
                        type: 'POST',
                        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                        data: {
                            'telefon': $('#lockInput').val()
                        },
                    }).done(function (response) {
                        console.log(response);
                        alertBoxSingle.text('');
                        if (response === 'locked') {
                            alertBoxSingle.append('<div class="alert alert-success" role="alert">\n' +
                                '  <strong>Udało się!</strong> Zablokowano numer ' +$('#lockInput').val()+
                                '</div>');
                        }
                        if(response === 'locked.new'){
                            alertBoxSingle.append('<div class="alert alert-success" role="alert">\n' +
                                '  <strong>Udało się!</strong> Zablokowano <strong>NOWY</strong> numer ' +$('#lockInput').val()+
                                '</div>');
                        }
                        if(response === 'already locked'){
                            alertBoxSingle.append('<div class="alert alert-info" role="alert">\n' +
                                '  <strong>Nie zablokowano!</strong> Ten numer został już zablokowany ' +$('#lockInput').val()+
                                '</div>');
                        }
                        datatable.ajax.reload();
                    }).fail(function (response) {
                        alertBoxSingle.text('');
                        alertBoxSingle.append('<div class="alert alert-danger" role="alert">\n' +
                            '  <strong>Coś poszło nie tak!</strong> '+
                            '</div>');

                    });
                }
            });
        }else{
            swal('Wpisz numer do zablokowania');
        }
        });

        function fillMultiPanelSecond(data){
            let panelBody = lockMultiPanelSecond.find('.panel-body');
            panelBody.empty();
            let alertBoxMultiSecond = $('<div>').attr('id','alertBoxMultiSecond');
            panelBody.append(alertBoxMultiSecond);
            let row = $('<div>').addClass('row');
            let table = $('<table>').addClass('table table-striped table-bordered');
            let thead = $('<thead>');
            let headTr = $('<tr>');

            let dataArray = data.dataArr;
            for(let j = 0; j< dataArray[0].length; j++){
                headTr.append($('<th>').append(dataArray[0][j]));
            }
            table.append(thead.append(headTr));
            let tbody = $('<tbody>');
            for(let i = 1; i< dataArray.length; i++){
                let bodyTr = $('<tr>');
                for(let j = 0; j< dataArray[i].length; j++){
                    bodyTr.append($('<td>').append(dataArray[i][j]));
                }
                tbody.append(bodyTr)
            }
            table.append(tbody);

            panelBody.append(row.append($('<div>').addClass('col-md-6').css('overflow-x','auto').append(table)));
            if(data.count > 1000000){
                alertBoxMultiSecond.append($('<div>').addClass('alert alert-danger').append('Zbyt duża liczba rekordów! Maksimum 1 000 000.'));
            }else{
                alertBoxMultiSecond.append($('<div>').addClass('alert alert-info').append('Liczba rekordów: '+data.count));
                row.append($('<div>').addClass('col-md-6')
                    .append(
                        $('<button>').addClass('btn btn-info btn-block')
                            .attr('id','lockMultiSecond')
                            .append('Zablokuj!')
                            .click(function () {
                                lockMultiSecondButtonHandler(data.count);
                            })
                    ));
            }
        }

        navTabs.click(function (e) {
            navTabs.children().removeClass();
            $(e.target).parent().addClass('active');
            $('.lockPanel').attr('hidden','hidden');
            if($(e.target).attr('href') === '#lockSingle'){
                lockSinglePanel.prop('hidden',false);
            }else if($(e.target).attr('href') === '#lockMulti'){
                lockMultiPanel.prop('hidden',false);
                alertBoxMulti.empty();
            }
        });

        lockFileInput.change(function (e) {
            alertBoxMulti.empty();
            if(lockFileInput[0].files.length === 1 && lockFileInput[0].files[0].type === "text/csv"){
                lockMultiButton.prop('disabled', false);

            }else{
                lockMultiButton.prop('disabled', true);
                lockFileInput[0].value = '';

                alertBoxMulti.append($('<div>').addClass('alert alert-danger').append('Zły format pliku. Wymagany .csv.'));
            }
        });

        lockMultiButton.click(function () {
            let formData = new FormData();
            formData.append('fileWithPhoneNumbers',lockFileInput[0].files[0]);
            $.ajax({
                url: "{{ route('api.lockMultiAjax') }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                processData: false,
                contentType: false,
                data: formData,
                success: function (response) {
                    lockMultiPanel.prop('hidden',true);
                    lockMultiPanelSecond.prop('hidden',false);
                    fillMultiPanelSecond(response);
                }
            });
        });

        function lockMultiSecondAjax(actualRecord, count){
            let formData = new FormData();
            formData.append('fileWithPhoneNumbers',lockFileInput[0].files[0]);
            formData.append('actualRecord', actualRecord);
            $.ajax({
                url: "{{ route('api.lockMultiSecondAjax') }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                processData: false,
                contentType: false,
                data: formData,
                success: function (response) {
                    if(response == 'error'){
                        swal.close();
                        swal('Nie znaleziono kolumny "telefon"');
                    }else{
                        if(response.actualRecord < count){
                            newRecordsLocked += response.notFoundPhonesToBlockCount;
                            oldRecordsLocked += response.foundPhonesToBlockCount;
                            let progress = Math.round(10000*actualRecord/count)/100;
                            $(swal.getContent()).find('strong').empty().append(
                                $('<div>').addClass('progress')
                                    .append($('<div>').addClass('progress-bar progress-bar-striped active')
                                        .attr('role','progressbar')
                                        .attr('aria-valuenow',progress)
                                        .attr('aria-valuemin',0)
                                        .attr('aria-valuemax',100)
                                        .css('width',progress+'%').append(progress+'%'))
                            );
                            lockMultiSecondAjax(response.actualRecord, count);
                        }else{
                            alertBoxMulti.empty().append($('<div>').addClass('alert alert-info')
                                .append('Zablokowane nowe: '+ newRecordsLocked).append($('<br>'))
                                .append('Zablokowane istniejące: '+ oldRecordsLocked).append($('<br>'))
                                .append('Już zablokowane: '+ (count-newRecordsLocked-oldRecordsLocked-1)).append($('<br>'))
                                .append('Wszystkie numery: '+ (count-1))
                            );
                            lockMultiPanel.prop('hidden',false);
                            lockMultiPanelSecond.prop('hidden',true);
                            swal.close();
                            datatable.ajax.reload();
                        }
                    }
                }
            });
        }

        function lockMultiSecondButtonHandler(count) {
            swal({
                title: 'Czy na pewno?',
                type: 'warning',
                html: 'Czy na pewno chcesz zablokować numery z przesłanej paczki?',
                showCloseButton: true,
                showCancelButton: true,
                confirmButtonText: 'Tak, zablokuj!'
            }).then(function (result) {
                if (result.value) {
                    swal({
                        title: 'Ładowawnie...',
                        html: 'To może chwilę zająć <strong></strong>',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        allowEnterKey: false,
                        onOpen: () => {
                        //swal.showLoading();
                    }
                });
                    lockMultiSecondAjax(1, count);
                }
            });
        }
    </script>
@endsection