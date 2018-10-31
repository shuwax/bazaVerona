<!-- Default Bootstrap Navbar -->
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <a class="navbar-brand" id="department" href="#"></a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                @if (!Auth::guest())
                        @if (Auth::user()->rodzaj == "Badania/Wysyłka")
                            <li><a href="{{ url('wysylka') }}">Wysyłka</a></li>
                            <li><a href="{{ url('badania') }}">Badania</a></li>
                        @endif
                        @if (Auth::user()->rodzaj == "Badania")
                            <li><a href="{{ url('badania') }}">Baza</a></li>
                        @endif
                        @if (Auth::user()->rodzaj == "Wysyłka")
                            <li><a href="{{ url('wysylka') }}">Wysyłka</a></li>
                        @endif
                            @if (Auth::user()->dep_id == 1)
                                <li><a href="{{ url('zgody') }}">Baza Exito</a></li>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                        Raporty Bazy <span class="caret"></span>
                                    </a>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="{{ url('raport') }}">Raport bazy</a></li>
                                        <li><a href="{{ url('raportplus') }}">Raport bazy + Miasta</a></li>
                                    </ul>
                                </li>
                            @endif

                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    Raporty Użytkownika <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="{{ url('raportuzytkownika') }}">Raport użytkownika</a></li>
                                    <li><a href="{{ url('raportuserplus') }}">Raport użytkownika + Miasta</a></li>
                                </ul>
                            </li>

                            @if (Auth::user()->id == 1  || Auth::user()->dep_id == 1)
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                        Wgraj rekordy przez CSV <span class="caret"></span>
                                    </a>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="{{ url('wgrajBisnode') }}">Bisnode</a></li>
                                        <li><a href="{{ url('wgrajEvent') }}">Event</a></li>
                                        <li><a href="{{ url('wgrajPomylki') }}">Pomyłki</a></li>
                                        <li><a href="{{ url('wgrajZgody') }}">Zgody</a></li>
                                    </ul>
                                </li>
                            @endif


                @endif
                    <li><a href="{{ url('historia') }}">Pobrane Rekordy</a></li>
                    <li><a href="{{ url('tempInsertData') }}">Podgląd rekordów</a></li>
                    <li><a href="{{ url('phoneNumberText') }}">Generowanie sms</a></li>

                    @if (Auth::user()->id == 133 || Auth::user()->id == 1 || Auth::user()->dep_id == 1 || Auth::user()->id == 109)
                        <li><a href="{{ url('phonenumberZipCodes') }}">Sprawdzenie kodów pocztowych dla telefonów</a></li>
                    @endif

                    @if (Auth::user()->id == 1 || Auth::user()->id == 105 || Auth::user()->id == 130 || Auth::user()->id == 112 || Auth::user()->dep_id == 1)
                        <li><a href="{{ url('odblokowanie') }}">Odblokowanie paczek</a></li>
                    @endif
                    @if (Auth::user()->id == 133 || Auth::user()->id == 1 || Auth::user()->id == 134 || Auth::user()->id == 145 || Auth::user()->dep_id == 1)
                        <li><a href="{{ url('locking') }}">Zablokuj</a></li>
                    @endif
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">

                @if (Auth::guest())
                    <li><a href="{{ route('login') }}">Zaloguj</a></li>
                    <li><a href="{{ route('register') }}">Zarejestruj</a></li>
                        @else
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    {{ Auth::user()->name }} {{ Auth::user()->last }} <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <a href="{{ route('logout') }}"
                                           onclick="event.preventDefault();
                                           document.getElementById('logout-form').submit();">
                                            Wyloguj
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </li>
                            @endif


                </li>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container-fluid -->
</nav>