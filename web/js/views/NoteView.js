/**
 * Вид для записи
 */
var NoteView = Backbone.View.extend({
	initialize: function() {
		var self = this;
		this.tagName = 'div';
		this.template = _.template( $('#note').html() );

		this.render = function() {
			$(this.el).html(this.template( {note: this.model.toJSON()} )); 
			return this;
		}

		this.model.bind('change', function() {
			$(".js-note").html(self.render().el);
		});
	}
});