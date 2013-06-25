/**
 * Вид для списка разделов и записей
 */
var NavListView = Backbone.View.extend({
	events: {
		"click .check-box": "toggle",
	},

	toggle: function(e) {
		$(e.target).toggleClass('active');
		var li = $(e.target).closest('li');
		this.collection.toggle(li.attr('class'), li.data('id'));
	},

	initialize: function() {
		var self = this;
		this.tagName = 'div';
		this.template = _.template( $('#nav_list').html() );

		this.render = function() {
			this.remove();
			$(this.el).html(this.template( {elements: this.collection.toJSON(), cur_dir_id: this.options.cur_dir_id} ));
			this.delegateEvents();
			return this;
		}

		this.bindEvents = function() {
			$(".js-sortable").sortable({ 
				placeholder: "ui-state-highlight",
				opacity: 0.8,
				axis: 'y',
				containment: ".js-sortable",
				distance: 10,
				handle: ".drag",
				tolerance: "pointer",
				stop: self.collection.savePositions 
			});
			return this;
		}

		$('body').on('click', '.drag', function() {
			return false;	
		});

		this.collection.bind('remove', function() {
			$(".js-nav-list").html( self.render().el );
		});

		this.collection.bind('change', function() {
			$(".js-nav-list").html( self.render().el );
		});
	}
});