/**
 * Вид для всплывающего окна
 */
var PopupView = Backbone.View.extend({
	events: {
		"click .js-ok": "ok",
		"click .js-cancel": "cancel"
	},

	ok: function(e) {
		if(typeof this.model.get('ok_callback') == 'function') this.model.get('ok_callback')();
		this.cancel();
	},

	cancel: function(e) {
		this.remove();
	},

	initialize: function() {
		var self = this;
		this.tagName = 'div';
		this.template = _.template( $('#popup').html() );

		this.render = function() {
			$(this.el).html(this.template( {data: this.model.toJSON()} ));
			this.delegateEvents();
			return this;
		}

		this.model.bind('change', function() {
			$(".js-note").html( self.render().el );
		});
	}
});