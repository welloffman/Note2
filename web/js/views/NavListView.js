/**
 * Вид для списка разделов и записей
 */
var NavListView = Backbone.View.extend({
	initialize: function() {
		var self = this;
		this.tagName = 'div';
		this.template = _.template( $('#nav_list').html() );

		this.render = function() {
			$(this.el).html(this.template( {elements: this.collection.toJSON(), cur_dir_id: this.options.cur_dir_id} ));
			bindEventes();
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

		bindEventes = function() {
			$(self.el).find('.check-box').on('click', function() {
				$(this).toggleClass('active');
			});
		}

		$('body').on('click', '.drag', function() {
			return false;	
		});
	}
});