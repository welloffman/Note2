var RegistrationView = Backbone.View.extend({
	events: {
		'click .reg-apply': 'regApply' 
	},

	initialize: function() {
		var self = this;
		this.tagName = 'div';
		this.template = _.template( $('#reg_block').html() );

		this.render = function() {
			this.remove();
			$(this.el).html(this.template( {model: this.model.toJSON()} ));
			this.delegateEvents();
			return this;
		}

		this.model.bind('change', function() {
			$(".register").html(self.render().el);
		});
	},

	regApply: function() {
		var self = this;
		var data = {
			email: $(this.el).find('.email_field').val(),
			password: $(this.el).find('.password_field').val()
		}

		$.post(this.model.get('root') + 'reg_apply', data, function(resp) {
			if(resp.success && !_.isEmpty(resp.error)) {
				self.model.set({error: resp.error, params: resp.params});
			} else if(resp.success) {
				self.model.set({params: resp.params});
				$(self.el).find('.auto_login').submit();
			}
		});
	}
});