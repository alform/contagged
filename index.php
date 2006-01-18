<?
  require_once('init.php');
  ldap_login();

  //prepare filter
  $ldapfilter = _makeldapfilter();

  //check public addressbook
  $sr = ldap_list($LDAP_CON,$conf[publicbook],$ldapfilter);
  $result1 = ldap_get_binentries($LDAP_CON, $sr);
  //check users private addressbook
  if(!empty($_SESSION[ldapab][binddn])){
    $sr = @ldap_list($LDAP_CON,
                    $conf[privatebook].','.$_SESSION[ldapab][binddn],
                    $ldapfilter);
    $result2 = ldap_get_binentries($LDAP_CON, $sr);
  }
  
  $result = array_merge($result1,$result2);

  // select entry template
  if($_REQUEST['export'] == 'csv'){
    $entrytpl = 'export_list_csv_entry.tpl';
  }else{
    $entrytpl = 'list_entry.tpl';
  }

  $list = '';
  if(count($result)==1 && $_REQUEST[search]){
    //only one result on a search -> display page
    header("Location: entry.php?dn=".$result[0][dn]);
    exit;
  }elseif(count($result)){
    $keys = array_keys($result);
    uksort($keys,"_namesort");
    foreach($keys as $key){
      tpl_entry($result[$key]);
      $list .= $smarty->fetch($entrytpl);
    }
  }

  //prepare templates
  tpl_std();
  tpl_markers(); //FIXME not needed anymore!?
  tpl_categories();
  tpl_timezone();
  tpl_country();
  $smarty->assign('list',$list);
  $smarty->assign('filter',$_REQUEST['filter']);
  $smarty->assign('marker',$_REQUEST['marker']);
  $smarty->assign('search',$_REQUEST['search']);
  //display templates
  if($_REQUEST['export'] == 'csv'){
    header("Content-Type: text/csv");
    header('Content-Disposition: Attachement; filename="ldapabexport.csv"');
    $smarty->display('export_list_csv.tpl');
  }else{
    //save location in session
    $_SESSION[ldapab][lastlocation]=$_SERVER["REQUEST_URI"];

    header('Content-Type: text/html; charset=utf-8');
    $smarty->display('header.tpl');
    $smarty->display('list_filter.tpl');
    $smarty->display('list.tpl');
    $smarty->display('footer.tpl');
  }

  //------- functions -----------//

  /**
   * callback function to sort entries by name
   * uses global $result
   */
  function _namesort($a,$b){
    global $result;
    $x = $result[$a][sn][0].$result[$a][givenName][0];
    $y = $result[$b][sn][0].$result[$b][givenName][0];
    return(strcasecmp($x,$y));
  }


  /**
   * Creates an LDAP filter from given request variables search or filter
   */
  function _makeldapfilter(){
    //handle given filter

    $filter = ldap_filterescape($_REQUEST['filter']);
    $search = ldap_filterescape($_REQUEST['search']);
    $org    = ldap_filterescape($_REQUEST['org']);
    $marker = ldap_filterescape($_REQUEST['marker']);
    $categories = ldap_filterescape($_REQUEST['categories']);
    $_SESSION[ldapab][filter] = $_REQUEST['filter'];
    if(empty($filter)) $filter='a';

    if(!empty($marker)){
      $ldapfilter = '(&(objectClass=contactPerson)';
      $marker = explode(',',$marker);
      foreach($marker as $m){
        $m = trim($m);
        $ldapfilter .= "(marker=$m)";
      }
      $ldapfilter .= ')';
    }elseif(!empty($categories)){
      $ldapfilter = "(&(objectClass=OXUserObject)(OXUserCategories=$categories))";
    }elseif(!empty($search)){
      $search = trim($search);
      $words=preg_split('/\s+/',$search);
      $filter='';
      foreach($words as $word){
        $filter .= "(|(|(sn=*$word*)(givenName=*$word*))(o=*$word*))";
      }
      $ldapfilter = "(&(objectClass=inetOrgPerson)$filter)";
    }elseif(!empty($org)){
      $ldapfilter = "(&(objectClass=inetOrgPerson)(o=$org))";
    }elseif($filter=='other'){
      $other='';
      for ($i=ord('a');$i<=ord('z');$i++){
        $other .= '(!(sn='.chr($i).'*))';
      }
      $ldapfilter = "(&(objectClass=inetOrgPerson)$other)";
    }elseif($filter=='*'){
      $ldapfilter = "(objectClass=inetOrgPerson)";
    }else{
      $ldapfilter = "(&(objectClass=inetOrgPerson)(sn=$filter*))";
    }
    return $ldapfilter;
  }
?>
