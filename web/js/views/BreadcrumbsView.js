/**
 * Вид для крошек
 */
var BreadcrumbsView = Backbone.View.extend({
	initialize: function() {
		var self = this;
		this.tagName = 'div';
		this.template = _.template( $('#breadcrumbs').html() );

		this.render = function() {
			$(this.el).html(this.template( {data: this.model.toJSON()} ));
			return this;
		}

		this.model.bind('change', function() {
			$(".breadcrumbs").html(self.render().el);
		});
	}
});