$(function() {

    var app = {
        DOM: {},
        init: function () {

            if (window.location.pathname == '/register') {

                this.DOM.form = $('form');
                
                this.DOM.form.email = this.DOM.form.find('input[name="email"]');
                this.DOM.form.pwd   = this.DOM.form.find('input[name="password"]');
                this.DOM.form.pwdc  = this.DOM.form.find('input[name="password_confirmation"]');

                this.DOM.form.email.group = this.DOM.form.email.closest('.form-group');
                this.DOM.form.pwd.group = this.DOM.form.pwd.closest('.form-group');

                this.DOM.form.submit( function(e) {
                    e.preventDefault();

                    var self = this;

                    error = {};

                    app.DOM.form.email.group.find('strong').text('');
                    app.DOM.form.pwd.group.find('strong').text('');

                    app.DOM.form.email.group.removeClass('has-error');
                    app.DOM.form.pwd.group.removeClass('has-error');

                    var user = {};

                    user.email = app.DOM.form.email.val();
                    user.password = app.DOM.form.pwd.val();
                    user.password_confirmation = app.DOM.form.pwdc.val();

                    var request = $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: '/validate/user',
                        type: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify(user)
                    });
                    request.done( function(data)
                    {
                        self.submit();
                    });
                    request.fail( function(jqXHR)
                    {
                        response = jqXHR.responseJSON;

                        if (response.errors.email) {
                            app.DOM.form.email.group.find('strong').text(response.errors.email[0]);
                            app.DOM.form.email.group.addClass('has-error');
                        }
                        if (response.errors.password) {
                            app.DOM.form.pwd.group.find('strong').text(response.errors.password[0]);
                            app.DOM.form.pwd.group.addClass('has-error');
                        }

                    });

                });
            }
        }
    }

    app.init();

});