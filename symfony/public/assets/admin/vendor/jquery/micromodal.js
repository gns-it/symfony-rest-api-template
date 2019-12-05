(function ($) {
    "use strict";

    /**
     * class window
     */
    class ChatModal {
        template = `<div id="${this.generateId()}" class="micro-modal" style="background-color:#f4efef; max-height:344px; width: 18rem; position: absolute">
                <div class="card-header rounded-0 bg-primary text-light">
                    <div class="row p-0">
                        <div class="col-md-8 align-middle header-text-box">

                        </div>
                        <div class="col-md-1 offset-1 text-right p-0">
                            <a href="#">
                                <i class="fa fa-window-maximize text-light" style="font-size: 14px"></i>
                            </a>
                        </div>
                        <div class="col-md-1 text-right p-0">
                            <a href="#">
                                <i class="fa fa-window-minimize text-light" style="font-size: 12px"></i>
                            </a>
                        </div>
                        <div class="col-md-1 text-right pl-2">
                            <a href="#">
                                <i class="fa fa-times text-light"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body pb-1" style="display: block" >
                    <div class="row p-0">
                        <div style="height: 230px" class="messages-box">
                        <span>

                        </span>
                        </div>
                        <div class="form-inline">
                            <div class="input-group input-group-sm mb-2 mr-sm-2">
                                <div class="input-group-prepend ">
                                    <div class="input-group-text"><a href="#">
                                            <i class="fa fa-smile text-warning"></i>
                                        </a>
                                    </div>
                                </div>
                                <input type="text" class="message-input form-control form-control-sm mr-2" placeholder="Message">
                                <button type="submit" class="submit-message btn btn-sm btn-success btn-circle btn-sm">
                                    <i class="fa fa-paper-plane"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;

        /**
         * @param header
         */
        constructor(header = 'No_header') {
            this.header = header;
            this.modal = $(this.template);
            this.headerTextBox = this.modal.find('.header-text-box');
            this.messageInput = this.modal.find('.message-input');
            this.submitCallbacks = [];
        }

        generateId() {
            this.modalId = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
            return this.modalId;
        }

        /**
         * @param text
         */
        pushMessage(text) {
            this.modal.find('.messages-box').append(`<span>${text}</span><br>`)
        }

        /**
         * @return ChatModal
         */
        open() {
            $('body').append(this.modal);
            this.modal.draggable();

            this.modal.css({top: 200, left: 900});

            this.headerTextBox.text(this.header);

            this.modal.on('click', '.fa-window-minimize', e => {
                this.modal.find('.card-body').hide()
            });
            this.modal.on('click', '.fa-window-maximize', e => {
                this.modal.find('.card-body').show()
            });
            this.modal.on('click', '.fa-times', e => {
                this.modal.remove();
            });

            this.modal.on('click', '.submit-message', e => {
                this.submitCallbacks.forEach(cb => {
                    cb(this.messageInput.val())
                });
                this.pushMessage(this.messageInput.val());
                this.emptyInput()
            });

            return this;
        };

        /**
         * @return ChatModal
         */
        emptyInput() {
            this.messageInput.val('');
            return this;
        };
        /**
         * @return ChatModal
         */
        onMessageSubmit(callback) {
            this.submitCallbacks.push(callback);
            return this;
        };
    }

    // (new ChatModal('Test')).open().onMessageSubmit(e => {
    //     console.log(e);
    // });


})(jQuery);