<?php

class ASHXML{
    /**
     * This function iterates over the keys from an array, if there is any
     * non-numeric keys, it is associative, else it is a "list"
     * @author Greg Gullett (greg@ecsquest.com)
     * @since 0.0.1
     * @param array $data
     * @return boolean
     */
	function _is_list($data){
	    $is_num = TRUE;
	    if (!is_array($data)){ return FALSE; }
	    foreach((array)$data as $key=>$value){
	        if (!is_numeric($key)){
	            $is_num = FALSE;
	        }
	    }
	    return $is_num;
	}

	/**
	 * Helper function that parses xml attributes from an array into a string
	 * @author Greg Gullett (greg@ecsquest.com)
	 * @since 0.0.1
	 * @param array $attrs
	 * @return string
	 */
	function _parse_attrs($attrs){
	    $attrString = "";
	    foreach($attrs as $key=>$value){
	        $attrString .= sprintf(" %s=\"%s\" ",$key, $value);
	    }
	    return $attrString;
	}

	/**
	 * Accepts an associative array and produces an XML document
	 * Attributes are supported by this function, see example.
	 * @author Greg Gullett (greg@ecsquest.com)
	 * @since 0.0.1
	 * @param array $data Associative array, can be multi-dimensional
	 * @return string The resulting XML document
	 *
	 * Example: (Not a valid USPS Request)
	 *  $test = array("RateRequestV4"=>array("@attr"=>array("ID"=>"This is my ID", "PASSWORD"=>"this is my pass")
	 *  									 "Package"=>array(array("Weight"=>1.0, "Service"=>"First Class"),
	 *  													  array("Weight"=>1.0, "Service"=>"PRIORITY"))
	 *  									)
     * 				 );
     * $xml_doc = $this->build_message($test);
     * -- Result --
     * <RateRequestV4 ID="This is my ID" PASSWORD="this is my pass">
     * <Package>
     * 	<Weight>1.0</Weight>
     *  <Service>First Class</Service>
     * </Package>
	 * <Package>
	 * 	<Weight>1.0</Weight>
	 *  <Service>PRIORITY</Service>
	 * </Package>
	 * </RateRequestV4>
	 */
	function build_message(array $data){
	    $xmlString = "";
	    foreach($data as $node=>$value){
	        if ($node == "@attr") { continue; }
	        $value_is_list = $this->_is_list($value);
	        if (is_array($value) && !$value_is_list){
	            $attrs = "";
	            if (array_key_exists("@attr",$value)){
	                $attrs = $this->_parse_attrs($value["@attr"]);
	                unset($value["@attr"]);
	            }
	            $xmlString .= "<".$node." ".$attrs.">\n";
                $xmlString .= $this->build_message($value);
                $xmlString .= "</".$node.">\n";
	            
	        }elseif(is_array($value) && $value_is_list){
	            foreach($value as $iter_node){
	                $temp = array($node=>$iter_node);
	                $xmlString .= $this->build_message($temp);
	            }
	        }else{
	            if (trim($value) != ""){
	                $xmlString .= "<".$node.">".$value."</".$node.">\n";
	            }else{
	                $xmlString .= "<".$node."/>\n";
	            }
	        }
	    }
	    return $xmlString;
	}
    
	/**
	 * Sets the header content type to text/xml and displays a given XML doc
	 * @author Greg Gullett (greg@ecsquest.com)
	 * @since 0.0.1
	 * @param string $xml_doc
	 */
    function show_xml($xml_doc){
        header("content-type: text/xml");
        print $xml_doc;
    }
    
	/**
	 * This is a helper function that retrieves an XML element from the
	 * provided document. Since we are trying to keep PHP4 support
	 * I cannot use simpleXML
	 * @author Greg Gullett (greg@ecsquest.com)
	 * @since 0.0.1
	 * @param string $element  The element to find in document
	 * @param string $document The XML Document to search
	 */
	function get($element, $document){
        preg_match_all('/<'.$element.'.*?>(.*?)<\/'.$element.'>/', $document, $matches);

        if (count($matches) > 1){
            return $matches[1];
        }
        return FALSE;
	}
    
}

/**
 *
 * This is a helper class for ASH-based / enabled Shipping plugins.
 * Helpful centralized functions will be stored here for ease of use.
 * @author Greg Gullett (greg@ecsquest)
 * @since 0.0.1
 */
class ASHTools{
    /**
     * Determines if the given zipcode is a US Military APO/AFO zip code
     * @author Greg Gullett (greg@ecsquest.com)
     * @since 0.0.1
     * @param int $zipcode
     * @return boolean
     */
    function is_military_zip($zipcode){
        $zips = array("09302","09734","96201","96202","96203","96204","96205","96206","96206","96207",
                "96208","96212","96213","96214","96215","96217","96218","96219","96220","96221",
                "96224","96251","96257","96258","96259","96260","96262","96264","96266","96267",
                "96269","96271","96275","96276","96278","96283","96284","96297","96306","96309",
                "96309","96310","96311","96311","96313","96319","96321","96322","96323","96326",
                "96328","96330","96336","96337","96338","96339","96339","96343","96347","96348",
                "96349","96350","96351","96351","96362","96365","96367","96368","96370","96372",
                "96373","96374","96375","96376","96377","96378","96379","96384","96386","96387",
                "96388","96388","96401","96402","96403","96404","96424","96425","96426","96427",
                "96490","96507","96507","96511","96515","96515","96517","96517",
                "96518","96520","96520","96521","96522","96530","96531","96531","96534","96535",
                "96536","96537","96538","96540","96541","96542","96543","96544","96546","96548",
                "96549","96550","96551","96553","96554","96555","96557","96557","96595","96595",
                "96598","96599","96601","96602","96603","96604","96605","96606","96607","96608",
                "96609","96610","96611","96612","96613","96613","96614","96614","96615","96615",
                "96616","96616","96617","96617","96619","96619","96620","96620","96621","96622",
                "96623","96624","96628","96629","96634","96635","96643","96657","96657","96660",
                "96661","96662","96663","96664","96665","96666","96667","96668","96669","96670",
                "96671","96672","96673","96674","96675","96677","96678","96679","96681","96681",
                "96682","96683","96684","96684","96686","96687","96698");
        
        if (in_array($zipcode, $zips)){
            return TRUE;
        }else{
            return FALSE;
        }
    }
    
    /**
     * Given an ISO country code, it will return the full country name
     * @author Greg Gullett (greg@ecsquest.com)
     * @since 0.0.1
     * @param string $short_country
     * @return string
     */
    function get_full_country($short_country){
        global $wpdb;
        if (!isset($wpdb)){
            return $short_country;
        }
		$full_name = $wpdb->get_var( $wpdb->prepare( "SELECT country
        							 FROM ".WPSC_TABLE_CURRENCY_LIST."
    							 	 WHERE isocode = %s", $short_country ) );
        return $full_name;
    }
    
    /**
     * Given a WPEC state code (int), will return the state/region name
     * @author Greg Gullett (greg@ecsquest.com)
     * @since 0.0.1
     * @param int $state_code
     * @return string|int will be int if wordpress database & wpec are not available
     */
    function get_state( $state_code ){
        global $wpdb;
        
		if ( ! defined ( "WPSC_TABLE_REGION_TAX") )
			return $state_code;
        
        $sql = $wpdb->prepare( "SELECT `".WPSC_TABLE_REGION_TAX."`.* FROM `".WPSC_TABLE_REGION_TAX."`
                                WHERE `".WPSC_TABLE_REGION_TAX."`.`id` = %d", $_POST['region'] );
        
		$dest_region_data = $wpdb->get_results( $sql, ARRAY_A );
        
		return ( is_array( $dest_region_data ) ) ? $dest_region_data[0]['code'] : "";
    }
    
    /**
     * Retrieves value for given key from $_POST or given session variable
     * You need to provide the session stub b/c it doenst know where you are looking
     * @author Greg Gullett (greg@ecsquest.com)
     * @since 0.0.1
     * @param mixed $key
     * @param array $session
     * @return mixed
     */
    function post_or_session($key, $session){
        if (array_key_exists($key, $_POST)){
            return $_POST[$key];
        }elseif(array_key_exists($key, $session)){
            return $session[$key];
        }
    }
    
    /**
     * Retrieves the destination from session or post as an array
     * or "state","country", and "zipcode"
     * @author Greg Gullett (greg@ecsquest.com)
     * @since 0.0.1
     * @param array $session
     * @return array
     */
    function get_destination($session){
        $address = array("state"=>$this->get_state($this->post_or_session("region",$session)),
                         "country"=>$this->post_or_session("country",$session),
                         "zipcode"=>$this->post_or_session("zipcode",$session)
                        );
        return $address;
    }
}

/**
 * Object representation of a package from a shipment.
 * This is the fundamental element of a shipment.
 * @author Greg Gullett (greg@ecsquest.com)
 * @since 0.0.1
 */
class ASHPackage{
    /**
     * Weight in pounds of the package
     * @var decimal
     */
    var $weight;
    /**
     * The height in inches of the package
     * @var decimal
     */
    var $height;
    /**
     * The length (longest part) of the package in inches
     * @var decimal
     */
    var $length;
    /**
     * The width of the package in inches
     * @var decimal
     */
    var $width;
    /**
     * Girth is defined, for a rectangle as G=2(Height+Width)
     * is auto calc'ed when you use set_dimensions
     * @var decimal
     */
    var $girth;
    /**
     * Whatever you want to describe what is in the package
     * @var string
     */
    var $contents;
    /**
     * The value/price of the item/package
     * @var decimal
     */
    var $value;
    /**
     * Flag denotes if the package has hazardous material or not
     * @var boolean
     */
    var $hazard = FALSE;
    /**
     * Flag denotes if the package is to have insurance added to the quote
     * @var boolean
     */
    var $insurance = FALSE;
    /**
     * The amount that the package is to be insured for
     * @var decimal
     */
    var $insured_amount;
    
    /**
     * The constructor for the ASHPackage class
     * Accepts an arguments array to fill out the class on initialization
     * @author Greg Gullett (greg@ecsquest.com)
     * @since 0.0.1
     * @param array $args
     */
    function ASHPackage(array $args = array()){
        foreach($args as $key=>$value){
            $this->$key=$value;
        }
    }
    /**
     * This is a "magic function" that will be used when I can convert to PHP5
     * When a property / function is set to private, this controls access
     * to outside functions
     * @author Greg Gullett (greg@ecsquest.com)
     * @since 0.0.1
     * @param string $item
     * @return mixed
     */
    function __get($item){
        return $this->$item;
    }
    
    /**
     * This is a "magic function" that sets a property that has as protected scope
     * only for php5
     * @author Greg Gullett (greg@ecsquest.com)
     * @since 0.0.1
     * @param string $item
     * @param mixed $value
     */
    function __set($item, $value){
        $this->$item = $value;
    }
    
    /**
     * This is a magic function that controls how the string representation of
     * the class looks / behaves.
     * @author Greg Gullett (greg@ecsquest.com)
     * @since 0.0.1
     */
    function __toString(){
        // Nothing here yet
    }
    
    /**
     * Sets the dimensions for the package given an array
     * array values should be "Height", "Length", "Width" and weight
     * girth is automatically calculated
     * @author Greg Gullett (greg@ecsquest.com)
     * @since 0.0.1
     * @param array $dimensions
     */
    function set_dimensions(array $dimensions){
        foreach($dimensions as $key=>$value){
            $this->$key = $value;
        }
        $this->girth = 2*($this->width+$this->height);
    }
    
}

/**
 * Object representation of a shipment of packages based on
 * the contents of a shopping cart
 * @author Greg Gullett (greg@ecsquest.com)
 * @since 0.0.1
 */
class ASHShipment{
    /**
     * An array of ASHPackage objects
     * @var array
     */
    var $packages=array();
    /**
     * Flag denotes if there are any hazardous packages in the shipment overall
     * @var boolean
     */
    var $hazard = FALSE;
    /**
     * The amount of packages in the shipment, automatically increments when
     * you use the add_package() function
     * @var int
     */
    var $package_count=0;
    /**
     * An array that represents the destination, (State,Zipode,Country)
     * @var array
     */
    var $destination = array();
    /**
     * The overall value of the contents of the shipment (all packages value summed together)
     * Automatically calculated when you use add_package()
     * @var decimal
     */
    var $shipment_value = 0;
    /**
     * The overal weight of the contents of the shipment (all packages weight summed together)
     * Automaticaly calculated when you use add_package()
     * @var unknown_type
     */
    var $total_weight = 0;
    
    /**
     * Constructor for the ASHShipment class
     * @author Greg Gullett (greg@ecsquest.com)
     * @since 0.0.1
     */
    function ASHShipment(){
    }

    /**
     * Sets the destination array using either post, session or provided array
     * @param string $internal_name internal name of shipping module
     * @param array $dest optional array if you already know destination.
     */
    function set_destination($internal_name, $dest=FALSE){
        if (!$dest){
            $tools = new ASHTools();
            if (!array_key_exists("wpec_ash",$_SESSION)){
                $_SESSION["wpec_ash"] = array();
            }
            $session_destination = (array_key_exists($internal_name,$_SESSION["wpec_ash"]) ? $_SESSION["wpec_ash"][$internal_name]["shipment"]["destination"] : array());
            $this->destination = $tools->get_destination($session_destination);
        }else{
            $this->destination = $dest;
        }
        
    }
    
    /**
     * This is a magic function that controls access to protected items
     * and allows you to retrieve their values (php5)
     * @param string $item
     * @return mixed
     */
    function __get($item){
        return $this->$item;
    }
    
    /**
     * This function sets the hazard flag on the class
     * while it seems inane, i am making sure that the values
     * are truly boolean true or false
     * @param boolean $flag
     */
    function set_hazard($flag){
        if ($flag == TRUE){
            $this->hazard = TRUE;
        }else{
            $this->hazard = FALSE;
        }
    }
    
    /**
     * Use this function to add a package object to the shipment.
     * it expects an object of class ASHPackage or throws an exception
     * @author Greg Gullett (greg@ecsquest.com)
     * @since 0.0.1
     * @param ASHPackage $package
     * @throws ErrorException
     */
    function add_package($package){
        if ($package instanceof ASHPackage){
            array_push($this->packages, $package);
            $this->package_count++;
            $this->total_weight += $package->weight;
            $this->shipment_value += $package->value;
        }else{
            $type = gettype($package);
            throw new ErrorException("ASHSHipment expected object of class ASHPackage, got instance of {$type} instead");
        }
    }
    
}

/**
 * This is the heart of the Advanced Shipping Helper for WPEC
 * It is the entrypoint for interaction between ASH and WPEC
 * @author Greg Gullett (greg@ecsquest.com)
 */
class ASH{
    /**
     * General constructor for ASH class
     * @author Greg Gullett (greg@ecsquest.com)
     */
    function ASH(){
    }

    /**
     * Builds a shipment object representing the cart contents from WPEC
     * @author Greg Gullett (greg@ecsquest.com)
     * @return ASHShipment
     */
    function get_shipment(){
        global $wpdb, $wpsc_cart;
        
        $shipment = new ASHShipment();
        if (!$wpsc_cart){
            return $shipment;
        }

        foreach($wpsc_cart->cart_items as $cart_item){
            $package = new ASHPackage();
            //*** Set package dimensions ***\\
            $dimensions = get_product_meta($cart_item->product_id, 'dimensions');
            $dim_array = array();
            $dim_array["weight"] = $cart_item->weight;
            $dim_array["height"] = ( !empty( $dimensions["height"] ) && is_numeric( $dimensions["height"] ) ) ? $dimensions["height"] : 1;
            $dim_array["width"]  = ( !empty( $dimensions["width"]  ) && is_numeric( $dimensions["width"]  ) ) ? $dimensions["width"]  : 1;
            $dim_array["length"] = ( !empty( $dimensions["length"] ) && is_numeric( $dimensions["length"] ) ) ? $dimensions["length"] : 1;
            $package->set_dimensions($dim_array);
            //*** Set other meta ***\\
            $package->hazard = (get_product_meta($cart_item->product_id,"ship_hazard") === FALSE) ? FALSE : TRUE;
            $package->insurance = get_product_meta($cart_item->product_id,"ship_insurance");
            $package->insured_amount = get_product_meta($cart_item->product_id,"ship_insured_amount");
            $package->value = $cart_item->unit_price;
            $package->contents = $cart_item->product_name;
            
            if ($shipment->hazard === FALSE and $package->hazard === TRUE){
                $shipment->set_hazard(TRUE);
            }
            $quantity = (int)$cart_item->quantity;

            for($i=1; $i <= $quantity; $i++){
                $shipment->add_package($package);
            }
        }
        return $shipment;
    }
    
    /**
     * Caches a result table for the given shipping module
     * @author Greg Gullett (greg@ecsquest.com)
     * @param string $internal_name
     * @param array $rate_table
     * @param ASHShipment $shipment
     */
    function cache_results($internal_name, $rate_table, $shipment){
        if (!is_array($_SESSION["wpec_ash"])){
            $_SESSION["wpec_ash"] = array();
        }
        if (!is_array($_SESSION["wpec_ash"][$internal_name])){
            $_SESSION["wpec_ash"][$internal_name] = array();
        }
        $_SESSION["wpec_ash"][$internal_name]["rate_table"] = $rate_table;
        $shipment_vals = array("package_count"=>$shipment->package_count,
                               "destination"  =>$shipment->destination,
                               "total_weight" =>$shipment->total_weight
            );
        $_SESSION["wpec_ash"][$internal_name]["shipment"] = $shipment_vals;
    }
    /**
     * Checks cached results for given shipping module and returns
     * the cached rates if nothing has changed.
     * @author Greg Gullett (greg@ecsquest.com)
     * @param string $internal_name
     * @param ASHShipment $shipment
     */
    function check_cache($internal_name, $shipment){
        if (!array_key_exists("wpec_ash", $_SESSION)){
            return FALSE;
        }
        if (!array_key_exists($internal_name,$_SESSION["wpec_ash"])){
            return FALSE;
        }
        if (is_object($_SESSION["wpec_ash"][$internal_name]["shipment"])){
            $cached_shipment = $_SESSION["wpec_ash"][$internal_name]["shipment"];
        }else{
            if (!empty($_SESSION["wpec_ash"][$internal_name]["shipment"])){
                if (is_array($_SESSION["wpec_ash"][$internal_name]["shipment"])){
                    $cached_shipment = $_SESSION["wpec_ash"][$internal_name]["shipment"];
                }
            }
        }
    
        $shipment_vals = array("package_count"=>$shipment->package_count,
                               "destination"  =>$shipment->destination,
                               "total_weight" =>$shipment->total_weight
            );
        if ($cached_shipment["package_count"] != $shipment->package_count){
            return FALSE;
        }elseif($cached_shipment["destination"] != $shipment_vals["destination"]){
            return FALSE;
        }elseif($cached_shipment["total_weight"] != $shipment_vals["total_weight"]){
            return FALSE;
        }else{
            return $_SESSION["wpec_ash"][$internal_name];
        }
       
    }

}
global $wpec_ash;
$wpec_ash = new ASH();
global $wpec_ash_xml;
$wpec_ash_xml = new ASHXML();
global $wpec_ash_tools;
$wpec_ash_tools = new ASHTools();
