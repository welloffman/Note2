var HomepageRouter = Backbone.Router.extend({
	registration: undefined,
	registration_view: undefined,

	routes: {
		'registration': 'registration'
	},

	initialize: function() {
		this.registration = new Registration({root: ROOT});
		this.registration_view = new RegistrationView({model: this.registration});		
	},

	registration: function() {
		$('.register').html(this.registration_view.render().el);
	}
});