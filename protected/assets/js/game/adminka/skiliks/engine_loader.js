//конфиги
php.include("js/game/adminka/config.js");

//плагины
php.include("js/game/adminka/jgridController.js");

//системные классы
php.include("js/game/frame_switcher.js");
php.include("js/game/lib/messages.js");
php.include("js/game/mouse.js");
php.include("js/game/game_logic.js");


//приемник, отправитель
php.include("js/game/skiliks/sender.js");
php.include("js/game/skiliks/receiver.js");

//
php.include("js/game/lib/loading.js");

//
php.include("js/game/adminka/menu_main.js");
php.include("js/game/adminka/world.js");


php.include("js/game/adminka/skiliks/characters_points_titles/characters_points_titles.js");
php.include("js/game/adminka/skiliks/dialog_branches/dialog_branches.js");
php.include("js/game/adminka/skiliks/dialogs/dialogs.js");
php.include("js/game/adminka/skiliks/events_results/events_results.js");
php.include("js/game/adminka/skiliks/events_samples/events_samples.js");
php.include("js/game/adminka/skiliks/events_choices/events_choices.js");
php.include("js/game/adminka/skiliks/scenario/scenario.js");
php.include("js/game/adminka/skiliks/logging/logging.js");

//стартер, обязательно последний инклуд // фикс под ИЕ
php.include("js/game/adminka/starter.js");
