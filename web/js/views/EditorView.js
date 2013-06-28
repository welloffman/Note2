/**
 * Вид для Редактора
 */
var EditorView = Backbone.View.extend({
	events: {
		'click .js-save': 'save',
		'click .js-cancel': 'cancel',
		'keypress .js-title' : 'keypress'
	},

	save: function() {
		var self = this;
		if(this.model.get("type") == "note") {
			tinyMCE.activeEditor.save();
			var new_data = {title: $(this.el).find('.js-title').val(), content: $("#mce").val()};
		} else {
			new_data = {title: $(this.el).find('.js-title').val()};
		}
		var cancel_button = $(this.el).find('.js-cancel');
		this.model.get("entity").save(new_data, {silent: true, callback: function() {
			self.cancel(null, true);
		}});
	},

	cancel: function(e, refresh) {
		tinyMCE.execCommand('mceRemoveControl', false, "mce");
		this.remove();
		var route = 'dir/' + this.model.get("entity").get("pid");
		
		if(this.model.get('type') == 'note' && this.model.get("entity").get("id")) {
			route += '/note/' + this.model.get("entity").get("id");
			refresh = true;
		}
		
		if(refresh) $("body").trigger({type: "route_force", route: route});
		else $("body").trigger({type: "route", route: route, replace: false});
		$("html,body").animate({scrollTop: 0}, 0);
	},

	keypress: function(e) {
		if(e.which == 13) this.save();
	},

	initialize: function() {
		var self = this;

		this.tagName = 'div';
		this.template = _.template( $('#editor').html() );

		this.render = function() {
			this.remove();
			$(this.el).html(this.template( {item: this.model.toJSON()} ));
			this.delegateEvents();
			return this;
		}

		this.model.bind('remove', function() {
			self.remove();
		});

		this.focus = function() {
			$(this.el).find('.js-title').focus();
		}
	}
});