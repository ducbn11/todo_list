<div class="header-container">
    <div class="header-container-item">
        <img src="{{URL::to('/img/avatar_default.png')}}">
        <div>{{data_get($user, 'name', '')}}</div>
    </div>
    <div class="header-container-item">
        <a href="{{route('logout')}}">Logout</a>
    </div>
</div>