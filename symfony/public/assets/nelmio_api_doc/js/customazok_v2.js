let area = $('<div id="draggable-area"></div>');
const body = $('body');
const Document = $(document);
(() => {
    setTimeout(() => {
        // ============ VARS START ============//

        let autoAuthMode = localStorage.getItem('aa') ? 1 : 0;
        let saveHistoryMode = localStorage.getItem('api-doc-save-history-mode') ? 1 : 0;
        let authorizeButton = $(".authorize");
        let authorizeDefaultButton = $(authorizeButton.clone());
        let showDocumentsButton = $(authorizeButton.clone());
        let defaultAuth = JSON.parse($("#swagger-data").html()).spec.security[0].default_auth;
        let clientCredentials = JSON.parse(defaultAuth.client_credentials);
        let userCredentials = JSON.parse(defaultAuth.user_credentials.replace(/\'/g, ''));
        let autoAuthButton = $(`<span style=\"cursor:pointer; color:white\"><small ${autoAuthMode ? 'style="background:#ee821b"' : ''} id=\"auto-auth\">auto auth</small></span>`);
        let switchUserSelect = $(`<select style="margin-right: 10px"></select>`);
        let saveHistoryButton = $(`<span style=\"cursor:pointer; color:white\"><small ${saveHistoryMode ? 'style="background:#78bc61"' : ''} id=\"save-history\">save history</small></span>`);
        let optBlockSections = $(".opblock-tag-section");
        let searchInput = $('<input type=\"search\" placeholder=\"Search tag\" id=\"search\">');

        // ============ VARS END ============//

        $(".info").find("h2").append(autoAuthButton).append(saveHistoryButton);

        // ============ SEARCH START ============//
        $(".auth-wrapper").prepend(searchInput);

        searchInput.keyup(c => {
            let b = $(c.target).val();
            optBlockSections.each(function (a, c) {
                return $(c).find(".nostyle").find("span").text().toLowerCase().match(new RegExp(b.toLowerCase())) ? void $(c).show() : void $(c).hide()
            });
        });

        // ============ SEARCH END ============//

        // ============ FAST SCROLL START ============//

        let toTopButton = $("<a id=\"toTop\"><span>&#9650</span></a>");
        let toBottomButton = $("<a id=\"toBottom\"><span>&#9660</span></a>");

        toTopButton.on("click", function (a) {
            a.preventDefault();
            $("html, body").animate({scrollTop: 0}, "300")
        });

        toBottomButton.on("click", function (a) {
            a.preventDefault();
            $("html, body").animate({scrollTop: Document.height()}, "300")
        });
        body.append(toTopButton).append(toBottomButton);

        $(window).scroll(function () {
            300 < $(window).scrollTop() ? toTopButton.show() : toTopButton.hide()
            300 < $(window).scrollTop() ? toBottomButton.hide() : toBottomButton.show()
        });
        // ============ FAST SCROLL END ============//


        // ============ BROWSE HISTORY START ============//

        body.on("click", ".opblock-tag, .opblock", d => {
            if (localStorage.getItem('api-doc-save-history-mode')) {
                let f = $(d.currentTarget).prop('id');
                window.history.pushState({}, ',', '#' + f);
            }
        });
        if (localStorage.getItem('api-doc-save-history-mode')) {

            Document.ready(e => {

                if (window.location.hash) {
                    let hash = window.location.hash.slice(1);
                    let top = '', opblock = false;
                    if (hash.match(new RegExp('operations-tag'))) {
                        top = hash;
                    } else {
                        top = 'operations-tag-' + hash.match(new RegExp('^(operations-)(.*)(-.*)$'))[2];
                        opblock = hash;
                    }

                    optBlockSections.each(function (i, item) {
                        if ($(item).find('h4').prop('id') === top) {
                            let b = $(`#${top}`);
                            b.parent().show(), b.click();
                            $([document.documentElement, document.body]).animate({
                                scrollTop: b.offset().top
                            }, 300);
                            if (opblock) {
                                setTimeout(() => {
                                    $(b).next('div').find('.opblock').each((i, e) => {
                                        if ($(e).prop('id') === opblock) {
                                            let input = $(e).find('a.nostyle');
                                            input.attr("tabindex", -1).focus();
                                            let customEvent = new Event('click', {bubbles: true});
                                            customEvent.simulated = true;
                                            input[0].dispatchEvent(customEvent);
                                            setTimeout(() => {
                                                input.closest('.is-open').find('.try-out__btn').click();
                                            }, 500);
                                        }
                                    })
                                }, 30)
                            }
                        }
                    });
                }
            });
        }


        Document.on('click', '#save-history', function (e) {

            window.history.pushState({}, ',', '#');
            if (localStorage.getItem('api-doc-save-history-mode')) {
                $(e.target).css('background', '#7d8492');
                return localStorage.removeItem('api-doc-save-history-mode');
            }
            $(e.target).css('background', '#78bc61');
            localStorage.setItem('api-doc-save-history-mode', 1);
        });

        // ============ BROWSE HISTORY END ============//

        // ============ AUTH START ============//

        authorizeDefaultButton.find("span").text("Authorize default (Ctrl + Alt + A)");
        authorizeDefaultButton.removeClass("authorize").addClass("authorize_default");
        authorizeDefaultButton.find("svg").detach();
        userCredentials.forEach((e, i) => {
            let option = $(`<option value="${e.password}">${e.username}</option>`);
            option.prop('selected', e.username === localStorage.getItem('current-username'));
            switchUserSelect.append(option);
        });
        authorizeDefaultButton.on("click", () => {
            let user = $(switchUserSelect.children().filter((i, e) => {
                return $(e).prop('value') === switchUserSelect.val();
            })[0]);
            let authData = {...clientCredentials, username: user.text(), password: user.val()};
            token = '';
            $.post("/api/v1/oauth/token", authData).done(a => {
                token = a.access_token
            })
        });

        switchUserSelect.change(e => {
            if (switchUserSelect.val() === '') {
                ascPass()
            }
            let user = $(switchUserSelect.children().filter((i, e) => {
                return $(e).prop('value') === switchUserSelect.val();
            })[0]);
            localStorage.setItem('current-username', user.text());
            authorizeDefaultButton.click()
        });

        function ascPass() {
            let modal = new tingle.modal({
                footer: true,
                stickyFooter: false,
                closeMethods: ['overlay', 'button', 'escape'],
                closeLabel: "Close",
                cssClass: ['custom-class-1', 'custom-class-2'],
                onOpen: function () {
                    console.log('modal open');
                },
                onClose: function () {
                    console.log('modal closed');
                },
                beforeClose: function () {
                    return true; // close the modal
                }
            });

            modal.setContent('<div class="swagger-ui"><h3 >Enter password</h3><input class="" type="password" placeholder="password"></div>');
            modal.addFooterBtn('Submit', 'tingle-btn tingle-btn--primary tingle-btn--pull-right', function () {
                modal.close();
            });
            modal.open();
        }

        showDocumentsButton.removeClass("authorize").data("href",'/develop/documentation/filtration');
        showDocumentsButton.find("span").text("Filtration");
        showDocumentsButton.find("svg").detach();
        showDocumentsButton.click(e => {
            window.open($(e.currentTarget).data('href'), '_blank');
        });
        authorizeButton.after(authorizeDefaultButton).after(showDocumentsButton).after(switchUserSelect);

        document.onkeyup = function (a) {
            a.ctrlKey && a.altKey && 65 === a.which && authorizeDefaultButton.trigger("click")
        };

        if (autoAuthMode) {
            authorizeDefaultButton.click()
        }

        Document.on('click', '#auto-auth', function (e) {

            if (localStorage.getItem('aa')) {
                $(e.target).css('background', '#7d8492');
                return localStorage.removeItem('aa');
            }
            $(e.target).css('background', '#ee821b');
            localStorage.setItem('aa', 1);
        });

        // ============ AUTH END ============//

    }, 500);

    // ============ DATE_PICKERS START ============//

    // Init datepickers
    body.on('click', '.try-out__btn', (e) => {
        let target = $(e.target);
        let section = target.closest('.opblock-section');
        let tbody = section.find('tbody');

        setTimeout(() => {
            let trs = tbody.find('tr');
            trs.each((i, e) => {
                let tr = $(e);
                if (/date/.test(tr.find('.prop-format').text())) {
                    let input = $(tr.find('input'));
                    input.datepicker({
                        dateFormat: "yy-mm-dd",
                        onClose: function (date) {
                            let ev2 = new Event('input', {bubbles: true});
                            ev2.simulated = true;
                            input[0].value = date;
                            input[0].dispatchEvent(ev2);
                        }
                    });
                }
            });

        }, 10);
    });
    // ============ DATE_PICKERS END ============//

    // ============ WINDOWS START ============//

    Document.on('click', '.close-frame', function (event) {
        $(event.target).closest('.iframe-container').remove();
    });
    body.append(area);
    Document.on('click', '.debug-link', function (event, a) {
        event.preventDefault();
        let target = $(event.target);
        let className = target.text();
        let url = target.prop('href');
        let token = target.data('token');

        let cWindow = new Window(className, url, area);
        let instance = cWindow.open();

        target.prop('target', 'frame' + className);
        let frame = instance.find('iframe');
        frame.load(function (e) {
            frame.contents().find("#header").remove();
            frame.contents().find("body")
                .append($("<style type='text/css'> #sidebar,#sidebar-shortcuts{background-color: #52555F} </style>"));
            frame.contents().find("#sidebar-shortcuts").css({"background-color": " #52555F"});
        });
    });

    // ============ WINDOWS END ============//

})();

/**
 * class window
 */
class Window {

    /**
     * @param className
     * @param src
     * @param area
     * @param callback
     */
    constructor(className, src, area, callback) {
        this.className = className;
        this.src = src;
        this.area = area;
        this.callback = callback;
    }

    /**
     * @return {jQuery.fn.init|jQuery|HTMLElement}
     */
    open() {

        let self = this;

        let cWindow = $(`<div class="window" style="position:fixed"></div>`);
        let title = $(`<span class="window-title" >${self.className}</span>`);
        let minimizeButton = $(`<a class="minimize"><i class="fa fa-window-minimize text-light" style="font-size: 12px"></i></a>`);
        let closeButton = $(`<a class="close-window"><i class="fa fa-times text-light"></i></a>`);
        let newTabButton = $(`<a href="${self.src}" target="_blank" class="new-tab"><i class="fa fa-window-maximize text-light" style="font-size: 12px"></i></a>`);
        let frame = $(`<iframe src="${self.src}" frameborder="0" name="frame${self.className}" class="frame"></iframe>`);

        /**
         * Add unique name
         */
        $(cWindow, title, minimizeButton, closeButton, newTabButton, frame).addClass(self.className);

        /**
         * Add nodes
         */
        cWindow.append([title, newTabButton, minimizeButton, closeButton, frame]);

        /**
         * Close window when new tab opening
         */
        $(newTabButton).click(() => {
            closeButton.click();
        });

        /**
         * Make window draggable
         */
        cWindow.draggable({
            iframeFix: true,
            containment: self.area,
            start: function () {
                $('.window').css('z-index', 1);
                cWindow.css('z-index', 2);
                let x = window.scrollX;
                let y = window.scrollY;
                window.onscroll = () => {
                    window.scrollTo(x, y);
                };
            },
            stop: function () {
                window.onscroll = () => {
                    window.scrollTo(0, 0);
                };
                window.onscroll = () => {
                };
            }
        });

        /**
         * Make window minimizable
         */
        minimizeButton.click(() => {
            if (cWindow.hasClass('mini')) {
                return cWindow.removeClass('mini');
            }
            cWindow.addClass('mini')
        });

        /**
         * Make window closable
         */
        closeButton.click(() => {
            cWindow.remove();
        });

        /**
         * Push window
         */
        body.append(cWindow);

        /**
         * Call callback
         */
        if (typeof this.callback === 'function') {
            self.callback(cWindow, frame, title, closeButton, newTabButton, minimizeButton);
        }

        return cWindow;
    };
}