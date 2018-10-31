@extends('main')

@section('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css"/>
    <link rel="stylesheet" href="https://formden.com/static/cdn/bootstrap-iso.css" />

    <!--Font Awesome (added because you use icons in your prepend/append)-->
    <link rel="stylesheet" href="https://formden.com/static/cdn/font-awesome/4.4.0/css/font-awesome.min.css" />

    <!-- Inline CSS based on choices in "Settings" tab -->
    <style>.bootstrap-iso .formden_header h2, .bootstrap-iso .formden_header p, .bootstrap-iso form{font-family: Arial, Helvetica, sans-serif; color: black}.bootstrap-iso form button, .bootstrap-iso form button:hover{color: white !important;} .asteriskField{color: red;}</style>

    <style>
    #pole1,#pole2,#pole3
    {
        height: 68px;
    }

    </style>

@endsection
@section('content')
    <h2>Osobisty raport wykorzystania bazy</h2>
    <p>Uwaga: Raport obejmuje osoby, korzystające z nowego systemu od dnia: 05.04.2017 </p>

    <form class="form-horizontal" method="post" action="raportuzytkownika">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
<div class="pole">
        <div id="pole1">
            <label class="control-label col-sm-2 requiredField" for="date">
                Data raportu (pojedyńczy dzień)
                <span class="asteriskField">
                *
               </span>
            </label>
            <div class="col-sm-10">
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar">
                        </i>
                    </div>
                    <input class="form-control" id="date" name="datejeden" placeholder="YYYY/MM/DD" type="text"/>
                </div>
            </div>
        </div>

        <div id="pole2">
            <label class="control-label col-sm-2 requiredField" for="date">
                Data raportu od
                <span class="asteriskField">
                *
               </span>
            </label>
            <div class="col-sm-10">
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar">
                        </i>
                    </div>
                    <input class="form-control" id="date" name="dateod" placeholder="YYYY/MM/DD" type="text"/>
                </div>
            </div>
        </div>

    <div id="pole3">
        <label class="control-label col-sm-2 requiredField" for="date">
            Data raportu do
            <span class="asteriskField">
                *
               </span>
        </label>
        <div class="col-sm-10">
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-calendar">
                    </i>
                </div>
                <input class="form-control" id="date" name="datedo" placeholder="YYYY/MM/DD" type="text"/>
            </div>
        </div>
    </div>
</div>



    <div class="form-group">
        <div class="col-sm-10 col-sm-offset-2">
            <button class="btn btn-primary " name="submit" type="submit">
                Generuj
            </button>
        </div>
    </div>


    <table class="table table-bordered">
        <thead>
        <tr>
            <th>NR</th>
            <th>Imie</th>
            <th>Nazwisko</th>
            <th>Bisnode</th>
            <th>Bisnode Zgody</th>
            <th>Zgody</th>
            <th>Zgody Nowe</th>
            <th>Event</th>
            <th>Event Zgody</th>
            <th>Reszta</th>
            <th>Reszta Zgody</th>
            <th>Exito</th>
            <th>Exito Zgody</th>
            <th>Suma</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
@endsection

@section('script')
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>


    <script>
        $(document).ready(function(){
            var date_input=$('input[name="datejeden"]'); //our date input has the name "date"
            var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
            date_input.datepicker({
                format: 'yyyy-mm-dd',
                container: container,
                todayHighlight: true,
                autoclose: true,
                startDate: "2017-04-05",
                endDate: 'today',
            })

            var date_input=$('input[name="dateod"]'); //our date input has the name "date"
            var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
            date_input.datepicker({
                format: 'yyyy-mm-dd',
                container: container,
                todayHighlight: true,
                autoclose: true,
                startDate: "2017-04-05",
                endDate: 'today',
            })

            var date_input=$('input[name="datedo"]'); //our date input has the name "date"
            var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
            date_input.datepicker({
                format: 'yyyy-mm-dd',
                container: container,
                todayHighlight: true,
                autoclose: true,
                startDate: "2017-04-05",
                endDate: 'today',
            })
        })


    </script>

@endsection