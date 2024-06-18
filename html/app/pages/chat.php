
    



    <div id="footer-bar" class="d-flex">
        <div class="me-2 ms-1 speach-icon">
            <button style="width: 40px; height: 40px; border-radius: 100px; margin-top:9px" data-menu="menu-upload" class="bg-gray-dark ms-2"><i class="fa fa-plus font-12 pt-2 "></i></button>
        </div>
        <div class="flex-fill speach-input">
            <input type="text" class="form-control" placeholder="Digite sua mensagem aqui">
        </div>
        <div class="ms-2 ms-1 speach-icon">
            <button style="width: 40px; height: 40px; border-radius: 100px; margin-top:9px" class="bg-blue-dark me-2"><i class="fa fa-arrow-up font-12 pt-2"></i></button>
        </div>
    </div>

    <div class="page-content header-clear-large">

        <div class="content my-0">
            
            <!-- Seen Group-->
            <div class="text-center pb-2">
                <span class="font-14 d-block mt-n1 opacity-50">Hoje</span>
            </div>
            
            <!-- Left Group-->
            <div class="d-flex">
                <div class="align-self-end">
                    <img src="images/empty.png" width="45" alt="img" class="rounded-xl me-3">
                </div>
                <div class="align-self-center">
                    <div class="bg-theme shadow-m px-3 py-2 rounded-m">
                        <p class="lh-base mb-0 color-theme">
                            Bom dia professora
                        </p>
                    </div>
                </div>
            </div>
            <div class="d-flex">
                <div class="align-self-end">
                    <img src="images/pictures/faces/<?= $_SESSION['app']['id_pessoa'] ?>.jpg" width="45" alt="img" class="rounded-xl me-3 mb-2">
                </div>
                <div class="align-self-center">
                    <div class="bg-theme shadow-m px-3 py-2 rounded-m">
                        <p class="lh-base mb-0 color-theme">
                            Pode me confirmar se nos dias 2 e 3 de maio terá aula?
                        </p>
                    </div>
                    <span class="font-12 ps-2 d-block mt-n1 opacity-50">08:37</span>
                </div>
            </div>
            
            <div class="mb-4"></div>
            
            <!-- Right Group -->
            <div class="d-flex">
                <div class="align-self-center ms-auto">
                    <div class="bg-blue-dark shadow-m px-3 py-2 rounded-m">
                        <p class="lh-base mb-0 color-white">
                            Não vai ter, no calendário você consegue ver quais dias serão recesso.
                        </p>
                    </div>
                    <span class="font-12 ps-3 d-block mt-n1 opacity-50 text-end">08:44</span>
                </div>
                <div class="align-self-end">
                    <img src="images/coordenacao/kelly.jpg" width="45" alt="img" class="rounded-xl ms-3 mb-2">
                </div>
            </div>
            
            <div class="mb-4"></div>
            
            <!-- Left Group -->
            <div class="d-flex">
                <div class="align-self-end">
                    <img src="images/pictures/faces/<?= $_SESSION['app']['id_pessoa'] ?>.jpg" width="45" alt="img" class="rounded-xl me-3 mb-2">
                </div>
                <div class="align-self-center">
                    <div class="bg-theme shadow-m px-3 py-2 rounded-m">
                        <p class="lh-base mb-0 color-theme">
                            Onde consigo ter acesso ao calendário?
                        </p>
                    </div>
                    <span class="font-12 ps-2 d-block mt-n1 opacity-50">09:03</span>
                </div>
            </div>
            
            <div class="mb-4"></div>
            
            <!-- Right Group -->
            <div class="d-flex mb-2">
                <div class="align-self-center ms-auto">
                    <div class="bg-blue-dark shadow-m rounded-m">
                        <p class="lh-base mb-0 color-white">
                            <img src="images/pictures/agenda.jpeg" alt="img" class="img-fluid rounded-m">
                        </p>
                    </div>
                </div>
                <div class="align-self-end">
                    <img src="images/empty.png" width="50" alt="img" class="rounded-xl me-3">
                </div>
            </div>    
            
            <div class="d-flex">
                <div class="align-self-center ms-auto">
                    <div class="bg-blue-dark shadow-m px-3 py-2 rounded-m">
                        <p class="lh-base mb-0 color-white">
                           Na agenda, nas primeiras páginas tem o calendário
                        </p>
                    </div>
                    <span class="font-12 p2-3 d-block mt-n1 opacity-50 text-end">09:05</span>
                </div>
                <div class="align-self-end">
                    <img src="images/coordenacao/kelly.jpg" width="45" alt="img" class="rounded-xl ms-3 mb-2">
                </div>
            </div>
            
            <div class="mb-4"></div>
            
            <!-- Left Group -->
            <div class="d-flex">
                <div class="align-self-end">
                    <img src="images/pictures/faces/<?= $_SESSION['app']['id_pessoa'] ?>.jpg" width="45" alt="img" class="rounded-xl me-3 mb-2">
                </div>
                <div class="align-self-center">
                    <div class="bg-theme shadow-m px-3 py-2 rounded-m">
                        <p class="lh-base mb-0 color-theme">
                            Obrigado!
                        </p>
                    </div>
                    <span class="font-12 ps-2 d-block mt-n1 opacity-50">09:12</span>
                </div>
            </div>
            

            
        </div>

    </div>
    <!-- End of Page Content-->

    <div id="menu-upload"
         class="menu menu-box-bottom menu-box-detached rounded-m"
         data-menu-height="auto"
         data-menu-effect="menu-over">
        <div class="list-group list-custom-small ps-2 me-4">
            <a href="#">
                <i class="font-14 fa fa-file color-gray-dark"></i>
                <span class="font-13">Arquivo</span>
                <i class="fa fa-angle-right"></i>
            </a>
            <a href="#">
                <i class="font-14 fa fa-image color-gray-dark"></i>
                <span class="font-13">Foto</span>
                <i class="fa fa-angle-right"></i>
            </a>
            <a href="#">
                <i class="font-14 fa fa-video color-gray-dark"></i>
                <span class="font-13">Video</span>
                <i class="fa fa-angle-right"></i>
            </a>

        </div>
    </div>
    
    <div id="menu-user-cog"
         class="menu menu-box-bottom menu-box-detached rounded-m"
        
         data-menu-effect="menu-over">
        <div class="list-group list-custom-small ps-2 me-4">
            <a href="#">
                <i class="font-14 fa fa-star color-yellow-dark"></i>
                <span class="font-13">Add to Favorites</span>
                <i class="fa fa-angle-right"></i>
            </a>
            <a href="#">
                <i class="font-14 fa fa-microphone color-gray-dark"></i>
                <span class="font-13">Call - Audio</span>
                <i class="fa fa-angle-right"></i>
            </a>
            <a href="#">
                <i class="font-14 fa fa-video color-blue-dark"></i>
                <span class="font-13">Call - Video</span>
                <i class="fa fa-angle-right"></i>
            </a>
            <a href="#">
                <i class="font-14 fa fa-map-marker color-green-dark"></i>
                <span class="font-13">Share Location</span>
                <i class="fa fa-angle-right"></i>
            </a>
            <a href="#">
                <i class="font-14 fa fa-times-circle color-red-dark"></i>
                <span class="font-13">Block John Wick</span>
                <i class="fa fa-angle-right"></i>
            </a>
        </div>
    </div>    
    
    
    <div id="menu-user"
         class="menu menu-box-right"
         data-menu-width="cover"
         data-menu-effect="menu-over">
         
         <div class="content text-center">
             <a href="#" class="close-menu icon icon-m mt-n2 notch-clear position-absolute start-0 color-theme"><i class="fa fa-chevron-left"></i></a>
             <img src="images/pictures/faces/1s.png" width="130" class="mx-auto rounded-circle">
             <i class="fa fa-circle color-green-dark mt-n3 d-block me-n5 ps-3 font-18"></i>
             <h1 class="font-800 font-30 mt-3 mb-n1">John Wick</h1>
             <span class="d-block pb-3 font-11">Last Seen: Searching for Dog</span>
             <div class="d-inline-block px-2">
                 <a href="#" class="icon icon-xxs bg-blue-dark rounded-xl"><i class="fa fa-user"></i></a><br>
                 <span class="font-10">Profile</span>
             </div>
             <div class="d-inline-block px-2">
                 <a href="#" class="icon icon-xxs bg-gray-dark rounded-xl"><i class="fa fa-bell"></i></a><br>
                 <span class="font-10">Mute</span>
             </div>
             <div class="d-inline-block px-2">
                 <a href="#" class="icon icon-xxs bg-red-dark rounded-xl"><i class="fa fa-flag"></i></a><br>
                 <span class="font-10">Report</span>
             </div>
         
            <div class="list-group list-custom-small text-start list-icon-0 pt-3">
                <a href="#">
                    <i class="font-14 fa fa-star color-yellow-dark"></i>
                    <span class="font-13">Add to Favorites</span>
                    <i class="fa fa-angle-right"></i>
                </a>
                <a href="#">
                    <i class="font-14 fa fa-comment color-magenta-dark"></i>
                    <span class="font-13">Start Private Chat</span>
                    <i class="fa fa-angle-right"></i>
                </a>
                <a href="#">
                    <i class="font-14 fa fa-microphone color-gray-dark"></i>
                    <span class="font-13">Call - Audio</span>
                    <i class="fa fa-angle-right"></i>
                </a>
                <a href="#">
                    <i class="font-14 fa fa-video color-blue-dark"></i>
                    <span class="font-13">Call - Video</span>
                    <i class="fa fa-angle-right"></i>
                </a>
                <a href="#">
                    <i class="font-14 fa fa-map-marker color-green-dark"></i>
                    <span class="font-13">Share Location</span>
                    <i class="fa fa-angle-right"></i>
                </a>
                <a href="#">
                    <i class="font-14 fa fa-times-circle color-red-dark"></i>
                    <span class="font-13">Block John Wick</span>
                    <i class="fa fa-angle-right"></i>
                </a>
            </div>
            <a href="#" class="close-menu btn btn-l btn-full shadow-l rounded-m bg-blue-dark text-uppercase font-800 mt-4">Back to Chat</a>
         </div>
    </div>




