    
<div id="page">
    
    <div class="header header-fixed header-logo-center">
        <a href="index.html" class="header-title">Sticky Mobile</a>
        <a href="#" data-back-button class="header-icon header-icon-1"><i class="fas fa-arrow-left"></i></a>
        <a href="#" data-toggle-theme class="header-icon header-icon-4"><i class="fas fa-lightbulb"></i></a>
    </div>
    
    <div id="footer-bar" class="footer-bar-1">
        <a href="index.html"><i class="fa fa-home"></i><span>Home</span></a>
        <a href="index-components.html"><i class="fa fa-star"></i><span>Features</span></a>
        <a href="index-pages.html" class="active-nav"><i class="fa fa-heart"></i><span>Pages</span></a>
        <a href="index-search.html"><i class="fa fa-search"></i><span>Search</span></a>
        <a href="#" data-menu="menu-settings"><i class="fa fa-cog"></i><span>Settings</span></a>
    </div>
        
    <div class="page-content header-clear-medium">
        
         <div class="card card-style">
            <div class="d-flex content">
                <div class="flex-grow-1">
                    <div>
                        <h1 class="font-700 mb-1">Jackson Doeson</h1>
                        <p class="mb-0 pb-1 pe-3">
                            Edit your Profile Settings here and apply. This is just a demo page. 
                        </p>
                    </div>
                </div>
                <div>
                    <img src="images/empty.png" data-src="images/pictures/faces/4s.png" width="80" class="rounded-circle mt- shadow-xl preload-img">
                </div>
            </div>
        </div>
        
        <div class="card card-style">
            <div class="content mt-0 mb-0">
                <div class="list-group list-custom-large list-icon-0">
                    <a data-toggle-theme data-trigger-switch="switch-dark-mode-a" href="#">
                        <i class="fa font-14 fa-lightbulb rounded-s color-yellow-dark"></i>
                        <span>Dark View</span>
                        <strong>Turn off the Lights</strong>
                        <div class="custom-control scale-switch ios-switch">
                            <input data-toggle-theme type="checkbox" class="ios-input" id="switch-dark-mode-a">
                            <label class="custom-control-label" for="switch-dark-mode-a"></label>
                        </div>
                        <i class="fa fa-chevron-right opacity-30"></i>
                    </a>        
                    <a data-trigger-switch="switch-dark-mode-a1" href="#">
                        <i class="fa font-14 fa-envelope rounded-s color-blue-dark"></i>
                        <span>Newsletter</span>
                        <strong>Get news and Updates?</strong>
                        <div class="custom-control scale-switch ios-switch">
                            <input type="checkbox" class="ios-input" id="switch-dark-mode-a1">
                            <label class="custom-control-label" for="switch-dark-mode-a1"></label>
                        </div>
                        <i class="fa fa-chevron-right opacity-30"></i>
                    </a>        
                    <a href="#">
                        <i class="fa font-14 fa-lock rounded-s color-red-dark"></i>
                        <span>2 Factor</span>
                        <strong>Cannot be Disabled</strong>
                        <div class="custom-control scale-switch ios-switch">
                            <input type="checkbox" class="ios-input" id="switch-c" checked disabled>
                            <label class="custom-control-label" for="switch-c"></label>
                        </div>
                        <i class="fa fa-chevron-right opacity-30"></i>
                    </a>        
                </div>
            </div>
        </div> 

        <div class="card card-style">
            <div class="content mb-0">
                <h2>Basic Information</h2>
                <p class="mb-4">
                    This contains basic profile fields, easily editable, set to disable or pre-populate with useful information.
                </p>
                <div class="input-style input-style-always-active has-borders has-icon validate-field">
                    <i class="fa fa-user font-12"></i>
                    <input type="name" class="form-control validate-name" id="f1" placeholder="Jackson Doe">
                    <label for="f1" class="color-blue-dark font-13">Name</label>
                    <i class="fa fa-times disabled invalid color-red-dark"></i>
                    <i class="fa fa-check disabled valid color-green-dark"></i>
                    <em>(required)</em>
                </div>
                
                <div class="input-style input-style-always-active has-borders has-icon validate-field mt-4">
                    <i class="fa fa-at font-12"></i>
                    <input type="email" class="form-control validate-name" id="f1a" placeholder="jack.doe@domain.com">
                    <label for="f1a" class="color-blue-dark font-13">Email</label>
                    <i class="fa fa-times disabled invalid color-red-dark"></i>
                    <i class="fa fa-check disabled valid color-green-dark"></i>
                    <em>(required)</em>
                </div>
                
                <div class="input-style input-style-always-active has-borders has-icon validate-field mt-4">
                    <i class="fa fa-map-marker font-12"></i>
                    <input type="text" class="form-control validate-name" id="f1abc" placeholder="Melbourne, Victoria">
                    <label for="f1abc" class="color-blue-dark font-13">Location</label>
                    <i class="fa fa-times disabled invalid color-red-dark"></i>
                    <i class="fa fa-check disabled valid color-green-dark"></i>
                    <em>(required)</em>
                </div>
                
                <div class="input-style input-style-always-active has-borders has-icon validate-field mt-4">
                    <i class="fa fa-phone font-12"></i>
                    <input type="tel" class="form-control validate-name" id="f1abcd" placeholder="+1 234 5678 9871">
                    <label for="f1abcd" class="color-blue-dark font-13">Phone</label>
                    <i class="fa fa-times disabled invalid color-red-dark"></i>
                    <i class="fa fa-check disabled valid color-green-dark"></i>
                    <em>(required)</em>
                </div>
                
                
                <a href="#" class="btn btn-full bg-green-dark btn-m text-uppercase rounded-sm shadow-l mb-3 mt-4 font-900">Save Basic Information</a>
            </div>
        </div>         
            
        <div class="card card-style">
            <div class="content mb-2">
                <h2>Social Profiles</h2>
                <p>
                    Activate options or set different elements here that are different from basic fields.
                </p>
                <div class="list-group list-custom-small list-icon-0">      
                    <a href="#">
                        <i class="fab font-14 fa-facebook-f rounded-s color-facebook"></i>
                        <span>Facebook</span>
                        <span class="badge text-uppercase bg-green-dark">Connected</span>
                        <i class="fa fa-chevron-right disabled"></i>
                    </a>        
                    <a href="#">
                        <i class="fab font-14 fa-twitter rounded-s color-twitter"></i>
                        <span>Twitter</span>
                        <span class="badge text-uppercase bg-green-dark">Connected</span>
                        <i class="fa fa-chevron-right disabled"></i>
                    </a>        
                    <a href="#">
                        <i class="fab font-14 fa-instagram rounded-s color-instagram"></i>
                        <span>Instagram</span>
                        <span class="badge text-uppercase bg-red-dark">ACCOUNT ERROR</span>
                        <i class="fa fa-chevron-right disabled"></i>
                    </a>        
                    <a class="border-0" href="#">
                        <i class="fab font-14 fa-linkedin-in rounded-s color-linkedin"></i>
                        <span>LinkedIn</span>
                        <span class="badge text-uppercase bg-yellow-dark">PENDING APPROVAL</span>
                        <i class="fa fa-chevron-right disabled"></i>
                    </a>        
                </div>
            </div>
        </div>
        
        <div class="card card-style">
            <div class="content mb-0">
                <h2 class="mb-0">Account Security</h2>
                <p class="mb-4">
                    Activate options or set different elements here that are different from basic fields.
                </p>
                <div class="input-style input-style-always-active has-borders no-icon validate-field">
                    <input type="password" class="form-control validate-text" id="f3c" value="1234&5678" placeholder="">
                    <label for="f3c" class="color-blue-dark font-12">Old Password</label>
                    <i class="fa fa-times disabled invalid color-red-dark"></i>
                    <i class="fa fa-check disabled valid color-green-dark"></i>
                    <em>(required)</em>
                </div>
                <div class="input-style input-style-always-active has-borders no-icon validate-field">
                    <input type="password" class="form-control validate-text" id="f3a" value="1234&5678" placeholder="">
                    <label for="f3a" class="color-blue-dark font-12">New Password</label>
                    <i class="fa fa-times disabled invalid color-red-dark"></i>
                    <i class="fa fa-check disabled valid color-green-dark"></i>
                    <em>(required)</em>
                </div>
                <div class="input-style input-style-always-active has-borders no-icon validate-field">
                    <input type="password" class="form-control validate-text" id="f3b" value="1234&5678" placeholder="">
                    <label for="f3b" class="color-blue-dark font-12">Confirm Password</label>
                    <i class="fa fa-times disabled invalid color-red-dark"></i>
                    <i class="fa fa-check disabled valid color-green-dark"></i>
                    <em>(required)</em>
                </div>
                <a href="#" class="btn btn-full bg-green-dark btn-m text-uppercase rounded-sm shadow-l mb-3 mt-4 font-900">Save Password</a>
            </div>
        </div> 
        
        <div class="footer card card-style">
            <a href="#" class="footer-title"><span class="color-highlight">StickyMobile</span></a>
            <p class="footer-text"><span>Made with <i class="fa fa-heart color-highlight font-16 ps-2 pe-2"></i> by Enabled</span><br><br>Powered by the best Mobile Website Developer on Envato Market. Elite Quality. Elite Products.</p>
            <div class="text-center mb-3">
                <a href="#" class="icon icon-xs rounded-sm shadow-l me-1 bg-facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="icon icon-xs rounded-sm shadow-l me-1 bg-twitter"><i class="fab fa-twitter"></i></a>
                <a href="#" class="icon icon-xs rounded-sm shadow-l me-1 bg-phone"><i class="fa fa-phone"></i></a>
                <a href="#" data-menu="menu-share" class="icon icon-xs rounded-sm me-1 shadow-l bg-red-dark"><i class="fa fa-share-alt"></i></a>
                <a href="#" class="back-to-top icon icon-xs rounded-sm shadow-l bg-dark-light"><i class="fa fa-angle-up"></i></a>
            </div>
            <p class="footer-copyright">Copyright &copy; Enabled <span id="copyright-year">2017</span>. All Rights Reserved.</p>
            <p class="footer-links"><a href="#" class="color-highlight">Privacy Policy</a> | <a href="#" class="color-highlight">Terms and Conditions</a> | <a href="#" class="back-to-top color-highlight"> Back to Top</a></p>
            <div class="clear"></div>
        </div>    
        
    </div>
    <!-- End of Page Content--> 
    
    <!-- All Menus, Action Sheets, Modals, Notifications, Toasts, Snackbars get Placed outside the <div class="page-content"> -->
    <div id="menu-settings" class="menu menu-box-bottom menu-box-detached">
        <div class="menu-title mt-0 pt-0"><h1>Settings</h1><p class="color-highlight">Flexible and Easy to Use</p><a href="#" class="close-menu"><i class="fa fa-times"></i></a></div>
        <div class="divider divider-margins mb-n2"></div>
        <div class="content">
            <div class="list-group list-custom-small">
                <a href="#" data-toggle-theme data-trigger-switch="switch-dark-mode" class="pb-2 ms-n1">
                    <i class="fa font-12 fa-moon rounded-s bg-highlight color-white me-3"></i>
                    <span>Dark Mode</span>
                    <div class="custom-control scale-switch ios-switch">
                        <input data-toggle-theme type="checkbox" class="ios-input" id="switch-dark-mode">
                        <label class="custom-control-label" for="switch-dark-mode"></label>
                    </div>
                    <i class="fa fa-angle-right"></i>
                </a>    
            </div>
            <div class="list-group list-custom-large">
                <a data-menu="menu-highlights" href="#">
                    <i class="fa font-14 fa-tint bg-green-dark rounded-s"></i>
                    <span>Page Highlight</span>
                    <strong>16 Colors Highlights Included</strong>
                    <span class="badge bg-highlight color-white">HOT</span>
                    <i class="fa fa-angle-right"></i>
                </a>        
                <a data-menu="menu-backgrounds" href="#" class="border-0">
                    <i class="fa font-14 fa-cog bg-blue-dark rounded-s"></i>
                    <span>Background Color</span>
                    <strong>10 Page Gradients Included</strong>
                    <span class="badge bg-highlight color-white">NEW</span>
                    <i class="fa fa-angle-right"></i>
                </a>        
            </div>
        </div>
    </div>
    <!-- Menu Settings Highlights-->
    <div id="menu-highlights" class="menu menu-box-bottom menu-box-detached">
        <div class="menu-title"><h1>Highlights</h1><p class="color-highlight">Any Element can have a Highlight Color</p><a href="#" class="close-menu"><i class="fa fa-times"></i></a></div>
        <div class="divider divider-margins mb-n2"></div>
        <div class="content">
            <div class="highlight-changer">
                <a href="#" data-change-highlight="blue"><i class="fa fa-circle color-blue-dark"></i><span class="color-blue-light">Default</span></a>
                <a href="#" data-change-highlight="red"><i class="fa fa-circle color-red-dark"></i><span class="color-red-light">Red</span></a>    
                <a href="#" data-change-highlight="orange"><i class="fa fa-circle color-orange-dark"></i><span class="color-orange-light">Orange</span></a>    
                <a href="#" data-change-highlight="pink2"><i class="fa fa-circle color-pink2-dark"></i><span class="color-pink-dark">Pink</span></a>    
                <a href="#" data-change-highlight="magenta"><i class="fa fa-circle color-magenta-dark"></i><span class="color-magenta-light">Purple</span></a>    
                <a href="#" data-change-highlight="aqua"><i class="fa fa-circle color-aqua-dark"></i><span class="color-aqua-light">Aqua</span></a>      
                <a href="#" data-change-highlight="teal"><i class="fa fa-circle color-teal-dark"></i><span class="color-teal-light">Teal</span></a>      
                <a href="#" data-change-highlight="mint"><i class="fa fa-circle color-mint-dark"></i><span class="color-mint-light">Mint</span></a>      
                <a href="#" data-change-highlight="green"><i class="fa fa-circle color-green-light"></i><span class="color-green-light">Green</span></a>    
                <a href="#" data-change-highlight="grass"><i class="fa fa-circle color-green-dark"></i><span class="color-green-dark">Grass</span></a>       
                <a href="#" data-change-highlight="sunny"><i class="fa fa-circle color-yellow-light"></i><span class="color-yellow-light">Sunny</span></a>    
                <a href="#" data-change-highlight="yellow"><i class="fa fa-circle color-yellow-dark"></i><span class="color-yellow-light">Goldish</span></a>
                <a href="#" data-change-highlight="brown"><i class="fa fa-circle color-brown-dark"></i><span class="color-brown-light">Wood</span></a>    
                <a href="#" data-change-highlight="night"><i class="fa fa-circle color-dark-dark"></i><span class="color-dark-light">Night</span></a>
                <a href="#" data-change-highlight="dark"><i class="fa fa-circle color-dark-light"></i><span class="color-dark-light">Dark</span></a>
                <div class="clearfix"></div>
            </div>
            <a href="#" data-menu="menu-settings" class="mb-3 btn btn-full btn-m rounded-sm bg-highlight shadow-xl text-uppercase font-900 mt-4">Back to Settings</a>
        </div>
    </div>    
    <!-- Menu Settings Backgrounds-->
    <div id="menu-backgrounds" class="menu menu-box-bottom menu-box-detached">
        <div class="menu-title"><h1>Backgrounds</h1><p class="color-highlight">Change Page Color Behind Content Boxes</p><a href="#" class="close-menu"><i class="fa fa-times"></i></a></div>
        <div class="divider divider-margins mb-n2"></div>
        <div class="content">
            <div class="background-changer">
                <a href="#" data-change-background="default"><i class="bg-theme"></i><span class="color-dark-dark">Default</span></a>
                <a href="#" data-change-background="plum"><i class="body-plum"></i><span class="color-plum-dark">Plum</span></a>
                <a href="#" data-change-background="magenta"><i class="body-magenta"></i><span class="color-dark-dark">Magenta</span></a>
                <a href="#" data-change-background="dark"><i class="body-dark"></i><span class="color-dark-dark">Dark</span></a>
                <a href="#" data-change-background="violet"><i class="body-violet"></i><span class="color-violet-dark">Violet</span></a>
                <a href="#" data-change-background="red"><i class="body-red"></i><span class="color-red-dark">Red</span></a>
                <a href="#" data-change-background="green"><i class="body-green"></i><span class="color-green-dark">Green</span></a>
                <a href="#" data-change-background="sky"><i class="body-sky"></i><span class="color-sky-dark">Sky</span></a>
                <a href="#" data-change-background="orange"><i class="body-orange"></i><span class="color-orange-dark">Orange</span></a>
                <a href="#" data-change-background="yellow"><i class="body-yellow"></i><span class="color-yellow-dark">Yellow</span></a>
                <div class="clearfix"></div>
            </div>
            <a href="#" data-menu="menu-settings" class="mb-3 btn btn-full btn-m rounded-sm bg-highlight shadow-xl text-uppercase font-900 mt-4">Back to Settings</a>
        </div>
    </div>
    <!-- Menu Share -->
    <div id="menu-share" class="menu menu-box-bottom menu-box-detached">
        <div class="menu-title mt-n1"><h1>Share the Love</h1><p class="color-highlight">Just Tap the Social Icon. We'll add the Link</p><a href="#" class="close-menu"><i class="fa fa-times"></i></a></div>
        <div class="content mb-0">
            <div class="divider mb-0"></div>
            <div class="list-group list-custom-small list-icon-0">
                <a href="auto_generated" class="shareToFacebook external-link">
                    <i class="font-18 fab fa-facebook-square color-facebook"></i>
                    <span class="font-13">Facebook</span>
                    <i class="fa fa-angle-right"></i>
                </a>
                <a href="auto_generated" class="shareToTwitter external-link">
                    <i class="font-18 fab fa-twitter-square color-twitter"></i>
                    <span class="font-13">Twitter</span>
                    <i class="fa fa-angle-right"></i>
                </a>
                <a href="auto_generated" class="shareToLinkedIn external-link">
                    <i class="font-18 fab fa-linkedin color-linkedin"></i>
                    <span class="font-13">LinkedIn</span>
                    <i class="fa fa-angle-right"></i>
                </a>        
                <a href="auto_generated" class="shareToWhatsApp external-link">
                    <i class="font-18 fab fa-whatsapp-square color-whatsapp"></i>
                    <span class="font-13">WhatsApp</span>
                    <i class="fa fa-angle-right"></i>
                </a>   
                <a href="auto_generated" class="shareToMail external-link border-0">
                    <i class="font-18 fa fa-envelope-square color-mail"></i>
                    <span class="font-13">Email</span>
                    <i class="fa fa-angle-right"></i>
                </a>
            </div>
        </div>
    </div>

</div>