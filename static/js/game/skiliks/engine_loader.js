//конфиги
php.include("/static/js/game/config/config.js" + window.gameVersion);
php.include("/static/js/game/config/imgConfig.js" + window.gameVersion);
php.include("/static/js/game/config/imgConfigMaps.js" + window.gameVersion);
php.include("/static/js/game/config/imgConfigPlayersLogos.js" + window.gameVersion);
php.include("/static/js/game/config/imgConfigPlayersLogosAdd.js" + window.gameVersion);
php.include("/static/js/game/config/imgConfigPlayers.js" + window.gameVersion);
php.include("/static/js/game/config/imgConfigCharacters.js" + window.gameVersion);
php.include("/static/js/game/config/imgConfigAnimations.js" + window.gameVersion);

//плагины
php.include("/static/js/game/adminka/jgridController.js" + window.gameVersion);

//системные классы
php.include("/static/js/game/input.js" + window.gameVersion);
php.include("/static/js/game/frame_switcher.js" + window.gameVersion);
php.include("/static/js/game/lib/messages.js" + window.gameVersion);
php.include("/static/js/game/mouse.js" + window.gameVersion);
php.include("/static/js/game/imageManager.js" + window.gameVersion);
php.include("/static/js/game/objects.js" + window.gameVersion);

//lib
php.include("/static/js/game/lib/mathematics.js" + window.gameVersion);
php.include("/static/js/game/lib/sounds.js" + window.gameVersion);
php.include("/static/js/game/lib/videos.js" + window.gameVersion);
php.include("/static/js/game/lib/keyboard.js" + window.gameVersion, 1, ['typeof(mailEmulator)','typeof(excel)']);
php.include("/static/js/game/lib/accounting.js" + window.gameVersion);
php.include("/static/js/game/lib/loading.js" + window.gameVersion);

//приемник, отправитель
php.include("/static/js/game/skiliks/sender.js" + window.gameVersion, 1, ['typeof(config)']);
php.include("/static/js/game/skiliks/receiver.js" + window.gameVersion, 1, ['typeof(config)']);
php.include("/static/js/game/skiliks/session.js" + window.gameVersion);

//движок самой игры
php.include("/static/js/game/dante.js" + window.gameVersion);
php.include("/static/js/game/game_logic.js" + window.gameVersion);
php.include("/static/js/game/drawGame.js" + window.gameVersion);


//загрузка игрового мира
php.include("/static/js/game/skiliks/world.js" + window.gameVersion);
php.include("/static/js/game/skiliks/simulation.js" + window.gameVersion);
php.include("/static/js/game/skiliks/events.js" + window.gameVersion);
php.include("/static/js/game/skiliks/dialogController.js" + window.gameVersion);
php.include("/static/js/game/skiliks/timer.js" + window.gameVersion);
php.include("/static/js/game/skiliks/icons.js" + window.gameVersion);
php.include("/static/js/game/skiliks/add_trigger.js" + window.gameVersion);
php.include("/static/js/game/skiliks/add_assessment.js" + window.gameVersion);
php.include("/static/js/game/skiliks/add_animation.js" + window.gameVersion);
php.include("/static/js/game/skiliks/add_documents.js" + window.gameVersion);
php.include("/static/js/game/skiliks/day_plan.js" + window.gameVersion);
php.include("/static/js/game/skiliks/excel.js" + window.gameVersion);
php.include("/static/js/game/skiliks/mail.js" + window.gameVersion);
php.include("/static/js/game/skiliks/documents.js" + window.gameVersion);
php.include("/static/js/game/skiliks/viewer.js" + window.gameVersion);
php.include("/static/js/game/skiliks/phone.js" + window.gameVersion);

//регистрация
php.include("/static/js/game/skiliks/register.js" + window.gameVersion);

//стартер, обязательно последний инклуд // фикс под ИЕ
php.include("/static/js/game/starter.js" + window.gameVersion, 1, ['typeof(config)','typeof(world)','typeof(sender)','typeof(receiver)']);
