simulation = {
    timer:0,
    varianceToUpdate:(60 * 1),
    bounds:{},
    divZindex:1,
    displayMode:'normal',

    screens:{
        'mainScreen':1,
        'plan':3,
        'mailEmulator':10,
        'phone':20,
        'visitor':30,
        'documents':40
    },
    screensSub:{
        'mainScreen':1,
        'plan':3,
        'mailMain':11,
        'mailPreview':12,
        'mailNew':13,
        'mailPlan':14,
        'phoneMain':21,
        'phoneTalk':23,
        'phoneCall':24,
        'visitorEntrance':31,
        'visitorTalk':32,
        'documents':41,
        'documentsFiles':42
    },
    screensActions:{
        'close':0,
        'open':1,
        'custom':2,
        'activated':'activated',
        'deactivated':'deactivated'
    },
    frontEventsLog:[],

    // it stores subscreen NAME for previosly opened screens
    // system can display only one subscreen for each scree, so I use screen name us array index 
    // used to find what sudscreen will be activated/deactivated
    subscreenTypesLog:[],

    // it stores subscreen PARAMETERS for previosly opened screens
    // system can display only one subscreen for each scree, so I use sereen name us array index
    // used to find parameters for sudscreen that must be activated/deactivated
    subscreenParametersLog:[],

    windowsArr:[],
    documentFilesArr:[],

    subWindowsArr:[],
    windowActive:'',
    subwindowActive:'', // for sim-close action log
    parametersActive:{}, // for sim-close action log

    isRecentlyIgnoredPhone:false
};