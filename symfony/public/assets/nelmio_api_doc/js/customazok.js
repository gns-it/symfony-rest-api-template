let area = $('<div id="draggable-area"></div>');
$(document).find('head').append('<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tour/0.11.0/css/bootstrap-tour-standalone.min.css" rel="stylesheet">')
$(document).find('head').append('<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tour/0.11.0/js/bootstrap-tour-standalone.min.js"></script>');
let body = $('body');
(() => {
    setTimeout(() => {

        body.on("click", ".opblock-tag, .opblock", d => {
            if (localStorage.getItem('api-doc-save-history-mode')) {
                let f = $(d.currentTarget).prop('id');
                window.history.pushState({}, ',', '#' + f);
            }
        });
        if (localStorage.getItem('api-doc-save-history-mode')) {

            $(document).ready(e => {

                if (window.location.hash) {
                    let hash = window.location.hash.slice(1);
                    let top = '', opblock = false;
                    if (hash.match(new RegExp('operations-tag'))) {
                        top = hash;
                    } else {
                        top = 'operations-tag-' + hash.match(new RegExp('^(operations-)(.*)(-.*)$'))[2];
                        opblock = hash;
                    }

                    $(".opblock-tag-section").each(function (i, c) {
                        if ($(c).find('h4').prop('id') === top) {
                            let b = $(`#${top}`);
                            b.parent().show(), b.click();
                            console.log('esdfr');
                            $([document.documentElement, document.body]).animate({
                                scrollTop: b.offset().top
                            }, 300);
                            if (opblock) {
                                setTimeout(() => {
                                    $(b).next('div').find('.opblock').each((i, e) => {
                                        if ($(e).prop('id') === opblock) {
                                            let input = $(e).find('a.nostyle');
                                            input.attr("tabindex", -1).focus();
                                            let ev2 = new Event('click', {bubbles: true});
                                            ev2.simulated = true;
                                            input[0].dispatchEvent(ev2);
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


        var tour = new Tour({
            steps: [
                {
                    element: ".authorize_default",
                    title: "Кнопка авторизации",
                    content: "Авторизация пользователем по умолчию",
                    onShow: function () {
                        $('.authorize_default').click()
                    },
                    placement: "left"
                },
                {
                    element: "#search",
                    title: "Поле поиска тегов",
                    content: "Поиск групп апи по названию",
                    placement: "right"
                },
                {
                    element: "#openAll",
                    title: "Закрыть/Раскрыть все теги",
                    content: "При большом колличестве следует подождать прогрузку",
                    placement: "bottom"
                },
                {
                    element: "#auto-auth",
                    title: "Автоматическая авторизация",
                    content: "Авторизует при загрузке страници",
                    placement: "bottom"
                },
                {
                    element: ".anchor:first",
                    title: "Ссылки",
                    content: "Принажатии копирует ссылку на тег",
                    placement: "left",
                    onNext: function (tour) {
                        $('.expand-operation:first').click();
                        setTimeout(function () {
                            $('.opblock-summary:first').click();

                            setTimeout(function () {
                                $('.try-out__btn:first').click();

                                setTimeout(function () {
                                    $('.execute:first').click();
                                }, 10);

                            }, 10);
                        }, 10);
                    }
                },
                {
                    delay: 2000,
                    element: ".debug-links",
                    title: "Окна дебагера",
                    content: "Принажатии открывает соответствующую страницу профайлера симфони",
                    placement: "left",
                },
                {
                    element: "#toTop",
                    title: "Наверх",
                    content: "Скролл вверх",
                    placement: "left",
                },
            ]
        });


        let aa = localStorage.getItem('aa') ? 1 : 0;
        let sh = localStorage.getItem('api-doc-save-history-mode') ? 1 : 0;
        const i = document.getElementsByClassName("opblock-tag no-desc");

        for (let b of i) {
            let c = $(b).prop("id");
            $(b).find('small').remove();
            let d = $("<small><span class=\"anchor\" title=\"Copy link\" style=\"float:right; cursor:alias \" data-id=\"" + c + "\">&#128279</span></small>");
            $(b).append(d)
            $(b).click((e) => {

                setTimeout(() => {
                    $(b).next('div').find('.opblock').each((i, e) => {
                        $(e).find('button').before("<small><span class=\"anchor-restart\" title=\"Copy link\" style=\"float:right; z-index: 2; cursor:alias \" data-id=\"" + i + '-' + c + "\">&#x21BB</span><span class=\"anchor-inner\" title=\"Copy link\" style=\"float:right; z-index: 2; cursor:alias \" data-id=\"" + i + '-' + c + "\">&#x2693</span></small>");
                    })
                }, 1)
            })
        }

        body.on("click", ".anchor-inner", d => {
            d.preventDefault(), d.stopPropagation();
            let e = $(d.target);
            e.addClass("red"), setTimeout(() => {
                e.removeClass("red")
            }, 200);
            let f = $(d.target).data("id"), a = window.location.href, b = a;
            0 < a.indexOf("?") && (b = a.substring(0, a.indexOf("?")), window.history.replaceState({}, document.title, b));
            let g = `${b}?a=${f}`, h = $("<input>");
            $("body").append(h), h.val(g).select(), document.execCommand("copy"), h.remove()
        });

        body.on('click', '.anchor-restart', d => {
            d.preventDefault();
            d.stopPropagation();

            let f = $(d.target).data("id"), a = window.location.href, b = a;

            0 < a.indexOf("?") && (b = a.substring(0, a.indexOf("?")), window.history.replaceState({}, document.title, b));

            window.location.href = window.location.href + '?f=' + f;
        });

        $(".anchor").click(d => {
            d.preventDefault(), d.stopPropagation();
            let e = $(d.target);
            e.addClass("red"), setTimeout(() => {
                e.removeClass("red")
            }, 200);
            let f = $(d.target).data("id"), a = window.location.href, b = a;
            0 < a.indexOf("?") && (b = a.substring(0, a.indexOf("?")), window.history.replaceState({}, document.title, b));
            let g = `${b}?a=${f}`, h = $("<input>");
            $("body").append(h), h.val(g).select(), document.execCommand("copy"), h.remove()
        });
        let c = $(".authorize"), f = $(c.clone());
        f.find("span").text("Authorize default (Ctrl + Alt + A)"), f.removeClass("authorize").addClass("authorize_default"), f.find("svg").detach();
        let d = JSON.parse(JSON.parse($("#swagger-data").html()).spec.security[0].default_auth);
        const e = $(".opblock-tag-section");
        f.on("click", () => {
            $.post("/api/v1/oauth/token", d).done(a => {
                token = a.access_token
            })
        }), c.after(f), $(".info").find("h2").append(`<span style=\"cursor:pointer; color:white\"><small id=\"openAll\">open all</small><small style=\"display:none\" id=\"closeAll\">close all</small></span><span style=\"cursor:pointer; color:white\"><small ${aa ? 'style="background:#ee821b"' : ''} id=\"auto-auth\">auto auth</small></span><span style=\"cursor:pointer; color:white\"><small ${sh ? 'style="background:#78bc61"' : ''} id=\"save-history\">save history</small></span>`), $(".auth-wrapper").prepend("<input type=\"search\" placeholder=\"Search tag\" id=\"search\"><button class='btn vocabulary'>Vocabulary</button>"), $("#openAll, #closeAll").click(a => {
            $(a.target).hide(), $(".expand-operation").click(), "closeAll" === $(a.target).prop("id") ? $("#openAll").show() : $("#closeAll").show()
        }), $("#search").keyup(c => {
            let b = $(c.target).val();
            e.each(function (a, c) {
                return $(c).find(".nostyle").find("span").text().toLowerCase().match(new RegExp(b.toLowerCase())) ? void $(c).show() : void $(c).hide()
            });
        });
        let g = new URLSearchParams(window.location.search), h = g.get("a");

        if (null !== h) {
            let index = -1;
            if (/^[0-9]+-/.test(h)) {
                index = h.substring(0, h.indexOf('-'));
                h = h.substring(h.indexOf('-') + 1);
            }
            $("#search").val(h.substring(15)).keyup(), $(e).hide();
            let b = $(`#${h}`);
            b.parent().show(), b.click();
            if (typeof index !== -1) {
                setTimeout(() => {
                    $(b).next('div').find('.opblock').each((i, e) => {
                        if (Number(i) === Number(index)) {
                            $(e).animate({backgroundColor: '#FF0000'}, 'slow');
                            $(e).animate({backgroundColor: '#ffffff'}, 'slow');
                        }
                    })
                }, 30)
            }
            let d = window.location.toString();
            if (0 < d.indexOf("?")) {
                let b = d.substring(0, d.indexOf("?"));
                window.history.replaceState({}, document.title, b)
            }
        }

        let res = g.get("f");

        if (null !== res) {

            let index = -1;
            if (/^[0-9]+-/.test(res)) {
                index = res.substring(0, res.indexOf('-'));
                res = res.substring(res.indexOf('-') + 1);
            }

            e.each(function (a, c) {

                if ($(c).find(".nostyle").find("span").text().toLowerCase().match(new RegExp(res.substring(15).replace('_', ' ').toLowerCase()))) {
                    let b = $(`#${res}`);
                    b.parent().show(), b.click();

                    if (typeof index !== -1) {
                        setTimeout(() => {
                            $(b).next('div').find('.opblock').each((i, e) => {

                                if (Number(i) === Number(index)) {
                                    let input = $(e).find('a.nostyle');
                                    input.attr("tabindex", -1).focus();
                                    let ev2 = new Event('click', {bubbles: true});
                                    ev2.simulated = true;
                                    input[0].dispatchEvent(ev2);
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

        $(document).on("click", "div.opblock-summary", b => {
            let c = $(b.target).closest("div.opblock");
            $("span.to_top").remove(), setTimeout(() => {
                c.hasClass("is-open") && c.closest("div.opblock").append("<span class=\"to_top\">&#9650</span>")
            }, 50)
        }), $(document).on("click", "span.to_top", a => {
            $("html, body").animate({scrollTop: $(a.target).closest("div.opblock").offset().top - 130}, 200)
        })
        if (aa) {
            $('.authorize_default').click()
        }
        $(document).ready(function () {
            tour.init();
            tour.start();
        });

        //=========================> Словарь

        const vocabulary = new Map();

        let routes = {
            // 'Category': {
            //     'path': '/api/v1/category/'
            // },
        };

        try {
            setTimeout(() => {

                if (token) {
                    Object.keys(routes).map(name => {

                        $.ajax({
                            url: routes[name]['path'],
                            headers: {
                                'Authorization': `Bearer ${token}`
                            },
                            method: 'GET',
                            dataType: 'json',
                            success: function (data) {

                                vocabulary.set(name, data);
                            }
                        })

                    });
                }

            }, 2000);

            function copyToClipboard(text) {
                if (window.clipboardData && window.clipboardData.setData) {
                    // IE specific code path to prevent textarea being shown while dialog is visible.
                    return clipboardData.setData("Text", text);

                } else if (document.queryCommandSupported && document.queryCommandSupported("copy")) {
                    var textarea = document.createElement("textarea");
                    textarea.textContent = text;
                    textarea.style.position = "fixed";  // Prevent scrolling to bottom of page in MS Edge.
                    document.body.appendChild(textarea);
                    textarea.select();
                    try {
                        return document.execCommand("copy");  // Security exception may be thrown by some browsers.
                    } catch (ex) {
                        console.warn("Copy to clipboard failed.", ex);
                        return false;
                    } finally {
                        document.body.removeChild(textarea);
                    }
                }
            }

            var modal = new tingle.modal({
                footer: true,
                stickyFooter: false,
                closeMethods: ['overlay', 'button', 'escape'],
                closeLabel: "Close",
                cssClass: ['custom-class-1', 'custom-class-2'],
                onOpen: function () {

                    var content = '';

                    for (var [key, value] of vocabulary.entries()) {

                        content += `<h4 align="left">${key}</h4><select name = "${key}" class="copy_to_clip select-css">`;

                        if (value.hasOwnProperty('items')) {
                            value = value['items'];
                        }

                        Object.keys(value).map(elem => {

                            content += ` <option value = "${value[elem]['uuid']}" selected>${value[elem]['name']}</option>`;

                        });

                        content += '</select><br>';

                    }

                    modal.setContent(content);
                },
                onClose: function () {
                    console.log('modal closed');
                },
                beforeClose: function () {
                    return true;
                }
            });

            $(document).on('change', '.copy_to_clip', e => {

                copyToClipboard($(e.target).val());
            });

            $(document).on('click', '.vocabulary', () => {

                modal.open();
            })

        } catch (e) {
            console.log(e);
        }
        //=====================================================>


    }, 500);
    let a = $("<a id=\"toTop\"><span>&#9650</span></a>");
    let b = $("<a id=\"toBottom\"><span>&#9660</span></a>");
    body.append(a),body.append(b), $(window).scroll(function () {
        300 < $(window).scrollTop() ? a.show() : a.hide()
        300 < $(window).scrollTop() ? b.hide() :  b.show()
    }), a.on("click", function (a) {
        a.preventDefault(), $("html, body").animate({scrollTop: 0}, "300")
    }),b.on("click", function (a) {
        a.preventDefault(), $("html, body").animate({scrollTop: $(document).height()}, "300")
    }), document.onkeyup = function (a) {
        a.ctrlKey && a.altKey && 65 === a.which && $("button.authorize_default").trigger("click")
    };
    $(document).on('click', '.close-frame', function (event) {
        $(event.target).closest('.iframe-container').remove();
    });
    $(document).on('keypress', 'div.is-open :input', function (event) {
        if (event.which === 13) {
            $(event.target).closest('div.is-open').find('.execute:first').focus().click();
        }
    });
    body.append(area);
    $(document).on('click', '#auto-auth', function (e) {

        if (localStorage.getItem('aa')) {
            $(e.target).css('background', '#7d8492');
            return localStorage.removeItem('aa');
        }
        $(e.target).css('background', '#ee821b');
        localStorage.setItem('aa', 1);
    });
    $(document).on('click', '#save-history', function (e) {

        window.history.pushState({}, ',', '#');
        if (localStorage.getItem('api-doc-save-history-mode')) {
            $(e.target).css('background', '#7d8492');
            return localStorage.removeItem('api-doc-save-history-mode');
        }
        $(e.target).css('background', '#78bc61');
        localStorage.setItem('api-doc-save-history-mode', 1);
    });
})();
let maxZindex = 1;
$(document).on('click', '.debug-link', function (event, a) {
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