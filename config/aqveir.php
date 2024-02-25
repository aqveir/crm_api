<?php

return [

    'settings' => [

        /*
        |--------------------------------------------------------------------------
        | Application Domain
        |--------------------------------------------------------------------------
        |
        | Specifies the Application Domian name used for accessing the console pages.
        | For example: {subdomain}.domainname.com
        |
        */
        'domain' => env('APPLICATION_DOAMIN', '{subdomain}.aqveir.in'),


        /*
        |--------------------------------------------------------------------------
        | Whitelisted Administration Subdomains
        |--------------------------------------------------------------------------
        |
        | Specifies the URL name used for accessing back-end pages.
        | For example: backend -> http://localhost/console
        |
        */
        'whitelisted_subdomains' => env('APPLICATION_WHITELIST_SUBDOAMIN', ['localhost', 'ellaisys']),


        /*
        |--------------------------------------------------------------------------
        | Back-end URI
        |--------------------------------------------------------------------------
        |
        | Specifies the URL name used for accessing front-end CRM pages.
        | For example: backend -> http://localhost/console
        |
        */
        'backend_uri' => env('APPLICATION_BACKEND_URI', '/console'),


        /*
        |--------------------------------------------------------------------------
        | Front-end Error URI
        |--------------------------------------------------------------------------
        |
        | Specifies the URI for path for the forntend error page.
        | For example: backend -> http://localhost/console
        |
        */
        'frontend_error_uri' => env('APPLICATION_FRONTEND_ERROR_URI', '/error/global/400'),

        
        /*
        |--------------------------------------------------------------------------
        | Specifies the default date format.
        |--------------------------------------------------------------------------
        */
        'date_format' => 'Y-m-d H:i:s',
        'date_format_response_generic' => 'c',

        'default' => [
            'role' => [
                'key_super_admin' => ['super_admin'],
                'key_organization_owner' => ['organization_owner']
            ]
        ],  


        /*
        |--------------------------------------------------------------------------
        | Specifies the restricted subdomains.
        |--------------------------------------------------------------------------
        */
        'restricted_subdomains' => [
            'abuse','ac','access','account','accounts','activate','ad','add','address',
            'adm','admanager','admin','administration','administrator','ads','adsense',
            'adult','advertising','adwords','affiliate','affiliates','ajax','album',
            'albums','all','analytics','anonymous','api','api1','api2','api3','app',
            'apple','arabic','archive','archives','assets','assets1','assets2','assets3',
            'assets4','assets5','atom','auth','authentication','avatar','awadhi',
            'azerbaijani','backup','banner','banners','beta','billing','bin','blog','blogs',
            'board','bot','bots','bottom','burmese','business','buy','cache','cadastro',
            'calendar','camo','campaign','cancel','careers','cart','center','cgi','changelog',
            'chat','checkout','chinese','client','cliente','clients','cname','code',
            'codereview','comercial','community','compare','compras','config','configuration',
            'connect','contact','contest','cpanel','create','css','css1','css2','css3',
            'dashboard','data','db','delete','demo','design','designer','dev','devel',
            'developer','developers','development','dir','direct_messages','directory',
            'doc','docs','documentation','domain','donate','download','downloads','dutch',
            'ecommerce','edit','editor','email','employment','english','enterprise','event',
            'events','facebook','faq','farsi','favorite','favorites','feed','feedback','feeds',
            'file','files','fleet','fleets','flog','follow','followers','following',
            'forum','forums','free','french','friend','friends','frontend','ftp',
            'gadget','gadgets','games','gan','german','gettingstarted','gist','git','github',
            'global','google','graph','graphs','group','groups','guest','gujarati','hakka',
            'hausa','help','hindi','home','homepage','host','hosting','hostmaster',
            'hostname','html','http','httpd','https','i','id','idea','ideas','ie',
            'image','images','imap','img','img1','img2','img3','imulus','index','indice',
            'info','information','intranet','invitations','invite','invoice','invoices',
            'io','ios','ipad','iphone','iq','ir','irc','is','it','italian','japanese',
            'java','javanese','javascript','jinyu','job','jobs','js','js1','js2','json',
            'kannada','knowledgebase','korean','lab','language','languages','left',
            'list','lists','log','login','logout','logs','lot','mail','mail1','mail2',
            'mail3','mail4','mail5','mailer','mailing','maithili','malayalam','manage',
            'manager','mandarin','map','maps','marathi','marketing','master','mautic',
            'media','memory','message','messenger','microblog','microblogs','mine',
            'min-nan','mis','mob','mobile','movie','movies','mp3','msg','msn','music',
            'musicas','mysql','name','named','net','network','networks','new','news',
            'newsite','newsletter','nick','nickname','notes','noticias','ns1','ns2',
            'ns3','ns4','ns5','nu','nz','oauth','oauth_clients','offers','old','online',
            'openid','operator','order','orders','organizations','oriya','page','pager',
            'pages','panel','panjabi','partner','partnerpage','partners','password',
            'payment','payments','perl','photo','photoalbum','photos','php','pic','pics',
            'plans','plugin','plugins','polish','pop','pop3','popular','portuguese',
            'post','postfix','postmaster','posts','present','president','press','privacy',
            'profile','project','projects','promo','pub','public','put','py','python',
            'qa','random','recruitment','redirect','register','registration','remember',
            'remove','replies','resolver','right','romanian','root','rss','ruby',
            'russian','sale','sales','sample','samples','sandbox','save','script',
            'scripts','search','secure','security','send','serbo-croatian','server',
            'servers','service','sessions','setting','settings','setup','shop','signin',
            'signup','sindhi','site','sitemap','sitenews','sites','sms','smtp','sony',
            'soporte','sorry','spanish','sql','ssh','ssl','ssladmin','ssladministrator',
            'sslwebmaster','stacks','stage','staging','start','stat','static','statistics',
            'stats','status','statuses','store','stores','stories','styleguide',
            'subdomain','subscribe','subscriptions','sunda','suporte','support',
            'supportdetails','support-details','survey','surveys','sv','svn','sy',
            'sysadmin','sysadministrator','system','tablet','tablets','talk','tamil',
            'task','tasks','tech','telnet','telugu','terms','test','test1','test2',
            'test3','teste','tests','thai','theme','themes','timeline','tmp','todo',
            'tools','top','tour','trac','translate','translations','trends','turkish',
            'twitter','uk','ukrainian','unfollow','unsubscribe','update','upload',
            'uploads','urdu','url','us','usage','user','username','users','usuario',
            'validation','validations','vendas','video','videos','vietnamese','visitor',
            'weather','web','webdisk','webmail','webmaster','website','websites',
            'whm','whois','widget','widgets','wiki','win','workshop','wws',
            'www','www1','www2','www3','www4','www5','www6','www7','wwws','wwww',
            'xfn','xiang','xml','xmpp','xpg','xxx','yahoo','yaml','yml','yoruba',
            'you','yourdomain','yourname','yoursite','yourusername','zuck',
            'fromdb','abstract','and','array','break','callable','case','catch',
            'class','clone','const','continue','declare','default','die','echo',
            'else','elseif','empty','enddeclare','endfor','endforeach','endif',
            'endswitch','endwhile','eval','exit','extends','final','for','foreach',
            'function','global','goto','implements','include','include_once',
            'instanceof','insteadof','interface','isset','list','namespace','new',
            'print','private','protected','public','require','require_once',
            'return','static','switch','throw','trait','try','unset','use','var',
            'while','xor','anal','boob','boobs','fuck','porn','pussy','sex', 's3x', 
        ],

        
        /*
        |--------------------------------------------------------------------------
        | Specifies the default region for infrastucture setup
        |--------------------------------------------------------------------------
        */
        'regions' => [
            'aqveir.com' => [
                'database' => [
                    'url' => env('DATABASE_URL'),
                    'host' => '127.0.0.1',
                    'port' => '3306',
                    'database' => env('DB_DATABASE', 'aqveir_db_com'),
                    'username' => env('DB_USERNAME', 'aqveir_db_user'),
                    'password' => 'Ellaisys@123',
                ],
                'elastic' => [

                ]
            ],
            'aqveir.net' => [

            ],
            'aqveir.co.in' => [

            ],

            'default' => 'aqveir.com'
        ],
     

        /*
        |--------------------------------------------------------------------------
        | Specifies the default cache settings by entities
        |--------------------------------------------------------------------------
        */
        'cache' => [
            'user' => [
                'key' => '_cache_user_key_',
                'duration_in_sec' => 86400,
            ],

            'catalogue' => [
                'category' => [
                    'key' => '_cache_catalogue_category_key_',
                    'duration_in_sec' => 86400,
                ]
            ]
        ],


        /*
        |--------------------------------------------------------------------------
        | Specifies the HTTP Status code
        |--------------------------------------------------------------------------
        */
        'http_status_code' => [
            'success' => 200
        ],

        'static' => [
            'key' => [
                'lookup_value' => [
                    //Contact Details Types
                    'phone' => 'contact_detail_type_phone',
                    'email' => 'contact_detail_type_email',

                    //Contact Address Types
                    'home'  => 'contact_address_type_home',
                    'work'  => 'contact_address_type_work'
                ]
            ],
            'value' => [

            ],
        ],


        /*
        |--------------------------------------------------------------------------
        | Specifies the external endpoints and configurations
        |--------------------------------------------------------------------------
        */
        'external_data' => [
            //Currency exchange rates
            'currency_exchange' => [
                'base_uri' => 'https://api.ratesapi.io/api',
                'timeout' => 60
            ],
        ],
    ],
];