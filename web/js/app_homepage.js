var ROOT = location.href.match(/app_dev\.php/) ? "/app_dev.php/" : "/";

$(function() {
	var app = new HomepageRouter();
	Backbone.history.start({pushState: true, root: ROOT});

	// Перехват ссылок - роутов бекбона для предотвращения перезагрузки всей страницы
	$("body").on('click', '.js-route', function(e) {
		e.preventDefault();
		app.navigate( $(this).attr('href'), true );
	});
});