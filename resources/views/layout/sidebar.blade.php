<style>
    .tit{
        /* background: #0e1726; */
        background: transparent;
        padding: 5px;
        width: 100%;
    }
    #sidebar ul.menu-categories li.menu > .dropdown-toggle[data-active="true"]{
        background: transparent !important;
    }
</style>

<nav id="sidebar">
    <div class="shadow-bottom"></div>
    <ul class="list-unstyled menu-categories" id="accordionExample" style="padding-top: 30px;">

        <div class="tit">Main</div>
        <li class="menu">
            <a href="{{route('admin.dashboard')}}" <?php if(request()->is('*dashboard*')){ echo 'data-active="true"';} ?> class="dropdown-toggle">
                <div class="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                    <span>Dashboard </span>
                </div>                
            </a> 
        </li>
        @if(Auth::user()->role == 1)

        <div class="tit">Manage Content</div>
        <li class="menu">
            <a href="#app" data-toggle="collapse" <?php if(request()->is('channel*')){ echo 'aria-expanded="true" data-active="true"';}else{echo 'aria-expanded="false"';} ?> class="dropdown-toggle">
                <div class="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-airplay"><path d="M5 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1"></path><polygon points="12 15 17 21 7 21 12 15"></polygon></svg>
                    <span>Channels</span>
                </div>
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </div>
            </a>
            <ul class="collapse submenu list-unstyled <?php if(request()->is('*channels*')){ echo 'show';} ?>" id="app" data-parent="#accordionExample">
                <li class="<?php if(request()->is('*channels')){ echo 'active';} ?>" >
                    <a href="{{route('admin.channels')}}"> List </a>
                </li>

                <li class="<?php if(request()->is('*channels-order')){ echo 'active';} ?>">
                    <a href="{{route('admin.channels.order')}}"> Change Order</a>
                </li>
            </ul>
        </li>

        <li class="menu">
            <a href="#app2" data-toggle="collapse" <?php if(request()->is('*movie*')){ echo 'aria-expanded="true" data-active="true"';}else{echo 'aria-expanded="false"';} ?> class="dropdown-toggle">
                <div class="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-film"><rect x="2" y="2" width="20" height="20" rx="2.18" ry="2.18"></rect><line x1="7" y1="2" x2="7" y2="22"></line><line x1="17" y1="2" x2="17" y2="22"></line><line x1="2" y1="12" x2="22" y2="12"></line><line x1="2" y1="7" x2="7" y2="7"></line><line x1="2" y1="17" x2="7" y2="17"></line><line x1="17" y1="17" x2="22" y2="17"></line><line x1="17" y1="7" x2="22" y2="7"></line></svg>
                    <span>Movie</span>
                </div>
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </div>
            </a>
            <ul class="collapse submenu list-unstyled <?php if(request()->is('*movie*') || request()->is('*above-18*')){ echo 'show';} ?>" id="app2" data-parent="#accordionExample">
                <li class="<?php if(request()->is('*movies')){ echo 'active';} ?>" >
                    <a href="{{route('admin.movies')}}">List </a>
                </li>
                <li class="<?php if(request()->is('*movies-order')){ echo 'active';} ?>">
                    <a href="{{route('admin.movie.order')}}">Order</a>
                </li>

                <li class="<?php if(request()->is('*movies-order-recent')){ echo 'active';} ?>">
                    <a href="{{route('admin.movie.order-recent')}}">Order Recent</a>
                </li>

                <li class="<?php if(request()->is('*movies-order-recent-sd')){ echo 'active';} ?>">
                    <a href="{{route('admin.movie.order-recent-sd')}}">Order Recent SD</a>
                </li>

                @if (Auth::User()->email == 'above-18@gmail.com' || env('IsDEveloper'))                    
                <li class="<?php if(request()->is('*above-18')){ echo 'active';} ?>" >
                    <a href="{{route('admin.adultmovies')}}"> Above 18 </a>
                </li>
                @endif


                {{-- <li class="<?php if(request()->is('*channels-order')){ echo 'active';} ?>">
                    <a href="{{route('admin.channels.order')}}"> Movie Links</a>
                </li> --}}

                {{-- <li class="<?php if(request()->is('*deleted-channel')){ echo 'active';} ?>">
                    <a href="{{route('admin.channels.deleted')}}"> Deleted Channel</a>
                </li> --}}
            </ul>
        </li>
        <li class="menu">
            <a href="#tvapp" data-toggle="collapse" <?php if(request()->is('*tvshow*') || request()->is('*tvchannel*')){ echo 'aria-expanded="true" data-active="true"';}else{echo 'aria-expanded="false"';} ?> class="dropdown-toggle">
                <div class="">
                    {{-- <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg> --}}
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="15" rx="2" ry="2"/><polyline points="17 2 12 7 7 2"/></svg>
                    <span>TV Show</span>
                </div>
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </div>
            </a>
            <ul class="collapse submenu list-unstyled <?php if(request()->is('*tvshow*') || request()->is('*tvchannel*')){ echo 'show';} ?>" id="tvapp" data-parent="#accordionExample">
                <li class="<?php if(request()->is('*tvchannel')){ echo 'active';} ?>" >
                    <a href="{{route('admin.tvchannel')}}"> Channels List </a>
                </li>
                <li class="<?php if(request()->is('*tvchannel-order')){ echo 'active';} ?>">
                    <a href="{{route('admin.tvchannel.order')}}"> Channels Order </a>
                </li>
                {{-- <li class="<?php if(request()->is('*tvshow')){ echo 'active';} ?>" >
                    <a href="{{route('admin.tvshow')}}"> Show List </a>
                </li> --}}
            </ul>
            
        </li>

        <li class="menu">
            <a href="#tvapppak" data-toggle="collapse" <?php if(request()->is('*pak-tv-channel*')){ echo 'aria-expanded="true" data-active="true"';}else{echo 'aria-expanded="false"';} ?> class="dropdown-toggle">
                <div class="">
                    {{-- <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg> --}}
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="15" rx="2" ry="2"/><polyline points="17 2 12 7 7 2"/></svg>
                    <span>TV Show Pak</span>
                </div>
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </div>
            </a>
            <ul class="collapse submenu list-unstyled <?php if(request()->is('*pak-tv-channel*') || request()->is('tv-channel-pak-order')){ echo 'show';} ?>" id="tvapppak" data-parent="#accordionExample">
                <li class="<?php if(request()->is('*tvchannel')){ echo 'active';} ?>" >
                    <a href="{{route('admin.tvchannelpak')}}"> Channels List </a>
                </li>
                <li class="<?php if(request()->is('tv-channel-pak-order')){ echo 'active';} ?>">
                    <a href="{{route('admin.tvchannelpak.order')}}"> Channels Order </a>
                </li>
                {{-- <li class="<?php if(request()->is('*tvshow')){ echo 'active';} ?>" >
                    <a href="{{route('admin.tvshow')}}"> Show List </a>
                </li> --}}
            </ul>
            
        </li>

        <li class="menu">
            <a href="#kidsapp" data-toggle="collapse" <?php if(request()->is('*kids-channel*') || request()->is('*kids-shows*') || request()->is('*kid-shows-season*') || request()->is('*kid-shows-episode*')){ echo 'aria-expanded="true" data-active="true"';}else{echo 'aria-expanded="false"';} ?> class="dropdown-toggle">
                <div class="">
                    {{-- <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg> --}}
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><circle cx="9" cy="10" r="1"/><circle cx="15" cy="10" r="1"/><path d="M9 15c1.5 1 4.5 1 6 0"/></svg>                    
                    <span>Kids Shows</span>
                </div>
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </div>
            </a>
            <ul class="collapse submenu list-unstyled <?php if(request()->is('*kids-channel*') || request()->is('*kids-shows*') || request()->is('*kid-shows-season*')|| request()->is('*kid-shows-episode*') ){ echo 'show';} ?>" id="kidsapp" data-parent="#accordionExample">
                <li class="<?php if(request()->is('*kids-channel')){ echo 'active';} ?>" >
                    <a href="{{route('admin.kidschannel')}}"> Channels List </a>
                </li>       
                <li class="<?php if(request()->is('*kids-channel-order')){ echo 'active';} ?>">
                    <a href="{{route('admin.kidschannel.order')}}"> Channels Order </a>
                </li>         
            </ul>
            
        </li>

        <li class="menu">
            <a href="#relapp" data-toggle="collapse" <?php if(request()->is('*religious-channel*') || request()->is('*religious-shows*') || request()->is('*religious-episodes*')){ echo 'aria-expanded="true" data-active="true"';}else{echo 'aria-expanded="false"';} ?> class="dropdown-toggle">
                <div class="">
                    {{-- <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg> --}}
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><line x1="12" y1="2" x2="12" y2="22"/><line x1="6" y1="8" x2="18" y2="8"/></svg>

                    <span>Religious</span>
                </div>
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </div>
            </a>
            <ul class="collapse submenu list-unstyled <?php if(request()->is('*religious-channel*') || request()->is('*religious-shows*') || request()->is('*religious-episodes*')){ echo 'show';} ?>" id="relapp" data-parent="#accordionExample">
                <li class="<?php if(request()->is('*religious-channel')){ echo 'active';} ?>" >
                    <a href="{{route('admin.RelChannel')}}"> Channels List </a>
                </li>                
                <li class="<?php if(request()->is('*religious-channel-order')){ echo 'active';} ?>">
                    <a href="{{route('admin.relchannel.order')}}"> Channels Order </a>
                </li>      
            </ul>
            
        </li>
        <li class="menu">
            <a href="#sports" data-toggle="collapse" <?php if(request()->is('*sports*') || request()->is('*sports*')){ echo 'aria-expanded="true" data-active="true"';}else{echo 'aria-expanded="false"';} ?> class="dropdown-toggle">
                <div class="">
                    {{-- <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg> --}}
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M8 16l2-6 2 6 2-6 2 6"/></svg>
                    <span>Sports</span>
                </div>
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </div>
            </a>
            <ul class="collapse submenu list-unstyled <?php if(request()->is('*sports*') || request()->is('*sports*')){ echo 'show';} ?>" id="sports" data-parent="#accordionExample">
                <li class="<?php if(request()->is('*sports')){ echo 'active';} ?>" >
                    <a href="{{route('admin.sportscategory')}}"> Category List </a>
                </li>
                <li class="<?php if(request()->is('*sports-order')){ echo 'active';} ?>">
                    <a href="{{route('admin.sportscategory.order')}}"> Category Order </a>
                </li> 
            </ul>
            
        </li>
        

        <li class="menu">
            <a href="#app3" data-toggle="collapse" <?php if(request()->is('*webseries*')){ echo 'aria-expanded="true" data-active="true"';}else{echo 'aria-expanded="false"';} ?> class="dropdown-toggle">
                <div class="">
                    {{-- <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-airplay"><path d="M5 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1"></path><polygon points="12 15 17 21 7 21 12 15"></polygon></svg> --}}
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2" ry="2"/><polygon points="10 9 15 12 10 15 10 9"/></svg>

                    <span>Webseries</span>
                </div>
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </div>
            </a>
            <ul class="collapse submenu list-unstyled <?php if(request()->is('*webseries*')){ echo 'show';} ?>" id="app3" data-parent="#accordionExample">
                <li class="<?php if(request()->is('*webseries')){ echo 'active';} ?>" >
                    <a href="{{route('admin.webseries')}}"> List </a>
                </li>
                 <li class="{{ request()->routeIs('admin.webseries.order') ? 'active' : '' }}">
                    <a href="{{ route('admin.webseries.order') }}"> Order </a>
                </li>
            </ul>
        </li>

        <li class="menu">
            <a href="#app4" data-toggle="collapse" <?php if(request()->is('*stage-shows*')){ echo 'aria-expanded="true" data-active="true"';}else{echo 'aria-expanded="false"';} ?> class="dropdown-toggle">
                <div class="">
                    {{-- <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-airplay"><path d="M5 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1"></path><polygon points="12 15 17 21 7 21 12 15"></polygon></svg> --}}
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2" ry="2"/><polygon points="10 9 15 12 10 15 10 9"/></svg>

                    <span>Stage Shows</span>
                </div>
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </div>
            </a>
            <ul class="collapse submenu list-unstyled <?php if(request()->is('*stage-shows*')){ echo 'show';} ?>" id="app4" data-parent="#accordionExample">
                <li class="<?php if(request()->is('*stage-shows*')){ echo 'active';} ?>" >
                    <a href="{{route('admin.stage-shows')}}"> List </a>
                </li>
                <li class="<?php if(request()->is('*stage-shows-order')){ echo 'active';} ?>">
                    <a href="{{route('admin.stageshowpak.order')}}"> Order </a>
                </li>                 
            </ul>
        </li>

        <li class="menu">
            <a href="#app5" data-toggle="collapse" <?php if(request()->is('*laughter-shows*')){ echo 'aria-expanded="true" data-active="true"';}else{echo 'aria-expanded="false"';} ?> class="dropdown-toggle">
                <div class="">
                    {{-- <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-airplay"><path d="M5 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1"></path><polygon points="12 15 17 21 7 21 12 15"></polygon></svg> --}}
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2" ry="2"/><polygon points="10 9 15 12 10 15 10 9"/></svg>

                    <span>Laughter Shows</span>
                </div>
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </div>
            </a>
            <ul class="collapse submenu list-unstyled <?php if(request()->is('*laughter-shows*')){ echo 'show';} ?>" id="app5" data-parent="#accordionExample">
                <li class="<?php if(request()->is('*laughter-shows')){ echo 'active';} ?>" >
                    <a href="{{route('admin.laughter-shows')}}"> List </a>
                </li>       
                <li class="<?php if(request()->is('*laughter-shows-order')){ echo 'active';} ?>">
                    <a href="{{route('admin.laughtershow.order')}}"> Order </a>
                </li>          
            </ul>
        </li>

        <div class="tit">Manage Specials</div>
        <li class="menu">
            <a href="#app1" data-toggle="collapse" <?php if(request()->is('*genre*')){ echo 'aria-expanded="true" data-active="true"';}else{echo 'aria-expanded="false"';} ?> class="dropdown-toggle">
                <div class="">
                    {{-- <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg> --}}
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 2c-2 2-6 2-6 6 0 6 2 10 6 10s6-4 6-10c0-4-4-4-6-6z"/><path d="M16 12c0 4 2 10 6 10s6-6 6-10-2-6-6-6-6 2-6 6z"/></svg>

                    <span>Genre</span>
                </div>
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </div>
            </a>
            <ul class="collapse submenu list-unstyled <?php if(request()->is('*genre*')){ echo 'show';} ?>" id="app1" data-parent="#accordionExample">
                <li class="<?php if(request()->is('*genres')){ echo 'active';} ?>" >
                    <a href="{{route('admin.genres')}}"> List </a>
                </li>

                <li class="<?php if(request()->is('*channels-order')){ echo 'active';} ?>">
                    <a href="{{route('admin.genre.order')}}"> Genre Order</a>
                </li>
            </ul>
        </li>
    
        <li class="menu">
            <a href="{{route('admin.languages')}}" <?php if(request()->is('*language*')){ echo 'data-active="true"';} ?> aria-expanded="false" class="dropdown-toggle">
                <div class="">
                    {{-- <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg> --}}
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 0-2-2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><path d="M9 10h6"/><path d="M12 7v6"/></svg>

                    <span>Language</span>
                </div>
            </a>
        </li>


        <li class="menu">
            <a href="{{route('admin.contentnetwork')}}" <?php if(request()->is('*contentnetwork*')){ echo 'data-active="true"';} ?> aria-expanded="false" class="dropdown-toggle">
                <div class="">
                    {{-- <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg> --}}
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 0 20"/><path d="M12 2a15.3 15.3 0 0 0 0 20"/></svg>
                    <span>Content Networks</span>
                </div>
            </a>
        </li>




        <li class="menu">
            <a href="{{route('admin.superAdminPlans')}}" <?php if(request()->is('*admin-plan*')){ echo 'data-active="true"';} ?> aria-expanded="false" class="dropdown-toggle">
                <div class="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trello"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><rect x="7" y="7" width="3" height="9"></rect><rect x="14" y="7" width="3" height="5"></rect></svg>
                    <span>Packages</span>
                </div>
            </a>
        </li>

        <li class="menu">
            <a href="{{route('admin.supreradmin.ads')}}" <?php if(request()->is('*super-admin-ads*')){ echo 'data-active="true"';} ?> aria-expanded="false" class="dropdown-toggle">
                <div class="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trello"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><rect x="7" y="7" width="3" height="9"></rect><rect x="14" y="7" width="3" height="5"></rect></svg>
                    <span>Ads</span>
                </div>
            </a>
        </li>
        <div class="tit">Miscellaneous</div>

        <li class="menu">
            <a href="{{route('admin.sliders')}}" <?php if(request()->is('*slider*')){ echo 'data-active="true"';} ?> aria-expanded="false" class="dropdown-toggle">
                <div class="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-image"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                    <span>Slider</span>
                </div>
            </a>
        </li>
        
        <li class="menu">
            <a href="{{route('admin.cdn_settings')}}" <?php if(request()->is('*cdn_settings*')){ echo 'data-active="true"';} ?> aria-expanded="false" class="dropdown-toggle">
                <div class="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-image"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                    <span>CDN Settings</span>
                </div>
            </a>
        </li>
        <li class="menu">
            <a href="{{route('admin.superAdminWallet')}}" <?php if(request()->is('*admin-wallet*')){ echo 'data-active="true"';} ?> aria-expanded="false" class="dropdown-toggle">
                <div class="">
                    {{-- {!! env('INR_SYMBOL') !!} --}}

                    {!! config('app.currency_icon') !!}
                    <span>Wallet Transactions</span>
                </div>
            </a>
        </li>        
        
        {{-- <li class="menu">
            <a href="{{route('admin.netadmins')}}" <?php if(request()->is('netadmins') || request()->is('add-netadmin')){ echo 'data-active="true"';} ?> aria-expanded="false" class="dropdown-toggle">
                <div class="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <span>Manage Net Admin</span>
                </div>
            </a>
        </li> --}}

        
        <div class="tit">Manage Users</div>
        <li class="menu">
            <a href="{{route('admin.admins')}}" <?php if(request()->is('admins') || request()->is('add-admin')){ echo 'data-active="true"';} ?> aria-expanded="false" class="dropdown-toggle">
                <div class="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <span>Admins</span>
                </div>
            </a>
        </li>
        <li class="menu">
            <a href="{{route('reseller.resellers')}}" <?php if(request()->is('*reseller*')){ echo 'data-active="true"';} ?> aria-expanded="false" class="dropdown-toggle">
                <div class="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <span>Resellers</span>
                </div>
            </a>
        </li>

        <li class="menu">
            <a href="{{route('admin.retailers')}}" <?php if(request()->is('*retailer*')){ echo 'data-active="true"';} ?> aria-expanded="false" class="dropdown-toggle">
                <div class="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <span>Retailors</span>
                </div>
            </a>
        </li>

        <li class="menu">
            <a href="{{route('admin.roles')}}" <?php if(request()->is('*role*')){ echo 'data-active="true"';} ?> aria-expanded="false" class="dropdown-toggle">
                <div class="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                    <span>Roles</span>
                </div>
            </a>
        </li>  
        
        <li class="menu">
            <a href="{{route('recent-watch-list')}}" <?php if(request()->is('*recent-watch-list*')){ echo 'data-active="true"';} ?> aria-expanded="false" class="dropdown-toggle">
                <div class="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                    <span>User History</span>
                </div>
            </a>
        </li>  
        @endif


        @if(Auth::user()->role == 2)
        <div class="tit">Manage Specials</div>
        <li class="menu">
            <a href="{{route('admin.adminPlans')}}" <?php if(request()->is('*admin-plan*')){ echo 'data-active="true"';} ?> aria-expanded="false" class="dropdown-toggle">
                <div class="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trello"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><rect x="7" y="7" width="3" height="9"></rect><rect x="14" y="7" width="3" height="5"></rect></svg>
                    <span>Packages</span>
                </div>
            </a>
        </li>

        {{-- <li class="menu">
            <a href="{{route('admin.admin.ads')}}" <?php if(request()->is('*admin.admin.ads*')){ echo 'data-active="true"';} ?> aria-expanded="false" class="dropdown-toggle">
                <div class="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trello"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><rect x="7" y="7" width="3" height="9"></rect><rect x="14" y="7" width="3" height="5"></rect></svg>
                    <span>Ads</span>
                </div>
            </a>
        </li> --}}

        <li class="menu">
            <a href="#app3" data-toggle="collapse" <?php if(request()->is('*ads*')){ echo 'aria-expanded="true" data-active="true"';}else{echo 'aria-expanded="false"';} ?> class="dropdown-toggle">
                <div class="">
                    {{-- <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-airplay"><path d="M5 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1"></path><polygon points="12 15 17 21 7 21 12 15"></polygon></svg> --}}
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2" ry="2"/><polygon points="10 9 15 12 10 15 10 9"/></svg>

                    <span>Ads</span>
                </div>
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </div>
            </a>
            <ul class="collapse submenu list-unstyled <?php if(request()->is('*ads*')){ echo 'show';} ?>" id="app3" data-parent="#accordionExample">
                <li class="<?php if(request()->is('*ads')){ echo 'active';} ?>" >
                    <a href="{{route('admin.admin.ads')}}">Admin Ads </a>
                </li>

                <li class="<?php if(request()->is('*adsplan')){ echo 'active';} ?>" >
                    <a href="{{route('admin.admin.adsplan')}}">Admin Ads Plan</a>
                </li>
            </ul>
        </li>
        <div class="tit">Miscellaneous</div>            
        <li class="menu">            
            <a href="{{route('admin.AdminWallet')}}" <?php if(request()->is('*admin-wallet*') || request()->is('add-reseller-wallet')){ echo 'data-active="true"';} ?> aria-expanded="false" class="dropdown-toggle">
                <div class="">
                    {{-- {!! env('INR_SYMBOL') !!}  --}}
                    {!! config('app.currency_icon') !!}
                    <span>Wallet Transactions</span>
                </div>
            </a>
        </li>
        
        <div class="tit">Manage Users</div>
        <li class="menu">
            <a href="{{route('reseller.resellers')}}" <?php if(request()->is('*resellers*')){ echo 'data-active="true"';} ?> aria-expanded="false" class="dropdown-toggle">
                <div class="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <span>Reseller</span>
                </div>
            </a>
        </li>
        @endif

        @if(Auth::user()->role == 3)
        <div class="tit">Miscellaneous</div>  
        <li class="menu">
            <a href="{{route('reseller.resellerWallet')}}" <?php if(request()->is('*reseller-wallet*')){ echo 'data-active="true"';} ?> aria-expanded="false" class="dropdown-toggle">
                <div class="">
                    {{-- {!! env('INR_SYMBOL') !!} --}}
                    {!! config('app.currency_icon') !!}
                    <span>Wallet Transactions</span>
                </div>
            </a>
        </li>
        <div class="tit">Manage Specials</div>
        <li class="menu">

            <a href="{{route('admin.retailers')}}" <?php if(request()->is('*retailers*')){ echo 'data-active="true"';} ?> aria-expanded="false" class="dropdown-toggle">
                <div class="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trello"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><rect x="7" y="7" width="3" height="9"></rect><rect x="14" y="7" width="3" height="5"></rect></svg>
                    <span>Retailers</span>
                </div>
            </a>


            <a href="{{route('reseller.resellerPlans')}}" <?php if(request()->is('*reseller-plan*')){ echo 'data-active="true"';} ?> aria-expanded="false" class="dropdown-toggle">
                <div class="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trello"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><rect x="7" y="7" width="3" height="9"></rect><rect x="14" y="7" width="3" height="5"></rect></svg>
                    <span>Packages</span>
                </div>
            </a>

            <a href="{{route('admin.reseller.ads')}}" <?php if(request()->is('*ads*')){ echo 'data-active="true"';} ?> aria-expanded="false" class="dropdown-toggle">
                <div class="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trello"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><rect x="7" y="7" width="3" height="9"></rect><rect x="14" y="7" width="3" height="5"></rect></svg>
                    <span>Ads</span>
                </div>
            </a>
        </li>
        @endif

        @if(Auth::user()->role == 4)
        <div class="tit">Miscellaneous</div>  
        <li class="menu">
            <a href="{{route('retailor.resellerWallet')}}" <?php if(request()->is('*reseller-wallet*')){ echo 'data-active="true"';} ?> aria-expanded="false" class="dropdown-toggle">
                <div class="">
                    {{-- {!! env('INR_SYMBOL') !!} --}}
                    {!! config('app.currency_icon') !!}
                    <span>Wallet Transactions</span>
                </div>
            </a>
        </li>
        <div class="tit">Manage Specials</div>
        <li class="menu">



            <a href="{{route('retailor.retailorPlans')}}" <?php if(request()->is('*retailor-plan*')){ echo 'data-active="true"';} ?> aria-expanded="false" class="dropdown-toggle">
                <div class="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trello"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><rect x="7" y="7" width="3" height="9"></rect><rect x="14" y="7" width="3" height="5"></rect></svg>
                    <span>Packages</span>
                </div>
            </a>

            <a href="{{route('admin.retailor.ads')}}" <?php if(request()->is('*ads*')){ echo 'data-active="true"';} ?> aria-expanded="false" class="dropdown-toggle">
                <div class="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trello"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><rect x="7" y="7" width="3" height="9"></rect><rect x="14" y="7" width="3" height="5"></rect></svg>
                    <span>Ads</span>
                </div>
            </a>
        </li>
        @endif

        @if(Auth::user()->role == 6)
        <li class="menu">
            <a href="{{route('netadmin.wallet')}}" <?php if(request()->is('*wallet*')){ echo 'data-active="true"';} ?> aria-expanded="false" class="dropdown-toggle">
                <div class="">
                    {{-- {!! env('INR_SYMBOL') !!}                     --}}
                    {!! config('app.currency_icon') !!}
                    <span>Wallet Transactions</span>
                </div>
            </a>
        </li>
        <li class="menu">
            <a href="{{route('netadmin.channels')}}" <?php if(request()->is('*channels*')){ echo 'data-active="true"';} ?> aria-expanded="false" class="dropdown-toggle">
                <div class="">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-airplay"><path d="M5 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1"></path><polygon points="12 15 17 21 7 21 12 15"></polygon></svg>
                    <span>Manage Channels</span>
                </div>
            </a>
        </li>
        @endif

        <li class="menu">
            <a href="{{route('user.users')}}" <?php if(request()->is('*user*')){ echo 'data-active="true"';} ?> aria-expanded="false" class="dropdown-toggle">
                <div class="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <span>Users</span>
                </div>
            </a>
        </li>

        @if(Auth::user()->role == 1)
        <li class="menu">
            <a href="{{route('url-settings-add')}}" <?php if(request()->is('*url-settings-add*')){ echo 'data-active="true"';} ?> aria-expanded="false" class="dropdown-toggle">
                <div class="">
                    <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                    <span>Settings</span>
                </div>
            </a>
        </li>

        <li class="menu">
            <a href="{{route('help-settings')}}" <?php if(request()->is('*help-settings*')){ echo 'data-active="true"';} ?> aria-expanded="false" class="dropdown-toggle">
                <div class="">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round">

                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2
                            19.79 19.79 0 0 1-8.63-3.07
                            19.5 19.5 0 0 1-6-6
                            19.79 19.79 0 0 1-3.07-8.67
                            A2 2 0 0 1 4.11 2h3
                            a2 2 0 0 1 2 1.72
                            c.12.81.37 1.6.72 2.34
                            a2 2 0 0 1-.45 2.11L8.09 9.91
                            a16 16 0 0 0 6 6l1.74-1.29
                            a2 2 0 0 1 2.11-.45
                            c.74.35 1.53.6 2.34.72
                            A2 2 0 0 1 22 16.92z">
                        </path>
                    </svg>
                    <span>Helpline</span>
                </div>
            </a>
        </li>
        @endif


    </ul>
    <!-- <div class="shadow-bottom"></div> -->

</nav>
