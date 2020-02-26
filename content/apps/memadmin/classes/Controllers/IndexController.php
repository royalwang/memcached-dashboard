<?phpdefine('APP_NAME', 'MEMADMIN');define('APP_VERSION', '3.0');RC_Session::start();class IndexController extends Royalcms\Component\Routing\Controller{        protected $view;    protected static $clusters = [];    const DEFAULT_CLUSTER = 'Default';    const STORAGE_KEY = 'memcache_servers';    	function __construct()    {//        header('Location: https://testapi.ecjia.com');		header('content-type: text/html; charset=' . RC_CHARSET);		header('Expires: Fri, 14 Mar 1980 20:53:00 GMT');		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');		header('Cache-Control: no-cache, must-revalidate');		header('Pragma: no-cache');			define('API_DEBUG', false);				$this->view = royalcms('view')->getSmarty();		// 模板目录		$this->view->setTemplateDir(SITE_THEME_PATH . RC_Theme::get_template() . DIRECTORY_SEPARATOR);        $this->view->addPluginsDir(SITE_THEME_PATH . RC_Theme::get_template() . DIRECTORY_SEPARATOR . 'smarty' . DIRECTORY_SEPARATOR);		$this->view->assign('theme_url', RC_Theme::get_template_directory_uri() . '/');    }	/**	 * 默认频道首页	 */	public function init()    {        $error = '<div class="alert alert-danger alert-dismissible fade show" style="display: none" role="alert">                        你的输入有误，请重新输入！                        <button type="button" class="close" data-hide="alert" aria-label="Close">                        <span aria-hidden="true">&times;</span>                        </button>                       </div>';        $this->view->assign('error', $error);        $server = RC_Session::get(self::STORAGE_KEY);        if (empty($server))        {            $this->view->display('index.php');        }else{            return rc_redirect(RC_Uri::url('memadmin/server/init'));        }	}	public function signin()    {        $host = royalcms('request')->input('host');        $host = trim($host);        $port = royalcms('request')->input('port');        $port = trim($port);        $server = $host . ':' . $port;        $exp = "/^(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$/";        $nexp = "/^(127\.0\.0\.1)|(localhost)|(10\.\d{1,3}\.\d{1,3}\.\d{1,3})|(172\.((1[6-9])|(2\d)|(3[01]))\.\d{1,3}\.\d{1,3})|(192\.168\.\d{1,3}\.\d{1,3})$/";        $reg = preg_match($exp, $host);        $regn = preg_match($nexp, $host);        if(empty($reg) || ! empty($regn) || ! is_numeric($port))        {            return rc_redirect(RC_Uri::url('memadmin/index/init'));        }        $cluster = array(            'hostname'  =>  $host,            'port'      =>  $port        );        self::$clusters[self::DEFAULT_CLUSTER] = array($server => $cluster);        RC_Session::push(self::STORAGE_KEY, serialize(self::$clusters));        return rc_redirect(RC_Uri::url('memadmin/index/init'));    }    /**     * 注销登陆     */    public  function logout()    {        RC_Session::flush();        return rc_redirect(RC_Uri::url('memadmin/index/init'));    }}// end