<?php


function register_my_menu()
{
    register_nav_menus(
        array(
            'top-menu' => __('Top Menu', 'absoluteyou'),
            'top-menu-fr' => __('Top Menu fr', 'absoluteyou'),
            'footer-menu' => __('Footer Menu', 'absoluteyou'),
            'footer-menu-fr' => __('Footer Menu fr', 'absoluteyou'),
            )
        );
    }
    add_action('init', 'register_my_menu');
    
    
    function add_custom_roles()
    {
        add_role(
            'employer',
            __('Employer'),
            array(
                'read'         => true,  // true allows this capability
                'edit_posts'   => false,
                )
            );
            
            add_role(
                'employee',
                __('Employee'),
                array(
                    'read'         => true,  // true allows this capability
                    'edit_posts'   => false,
                    )
                );
                
                add_role(
                    'sales',
                    __('Sales Person'),
                    array(
                        'read'         => true,  // true allows this capability
                        )
                    );
                    
                }
                add_action('init', 'add_custom_roles');
                
                
                function custom_partnership_post()
                {
                    
                    register_post_type('Partnership', array(
                        'labels' => array(
                            'name' => 'Partnership',
                            'singular_name' => 'Partnership',
                        ),
                        'capability_type' => 'Partnership',
                        'capabilities' => array(
                            'edit_post'          => 'edit_Partnership',
                            'read_post'          => 'read_Partnership',
                            'delete_post'        => 'delete_Partnerships',
                            'edit_posts'         => 'edit_Partnership',
                            'edit_others_posts'  => 'edit_others_Partnership',
                            'publish_posts'      => 'publish_Partnership',
                            'read_private_posts' => 'read_private_Partnership',
                            'create_posts'       => 'edit_Partnership',
                        ),
                        'description' => 'All the Partnership for absolute you',
                        'public' => true,
                        'menu_position' => 20,
                        'menu_icon' => 'dashicons-admin-site',
                        'supports' => array('title', 'editor', 'custom-fields', 'subtitle', 'thumbnail', 'post-attributes'),
                        'show_in_rest' => true,
                        'rewrite' => array(
                            "slug" => 'partnership'
                            )
                        ));
                    }
                    
                    add_action('init', 'custom_partnership_post');
                    
                    
                    function RegisterEmployerBeBright()
                    {
                        global $post;
                        $postId = $_POST['postId'];
                        
                        
                        //$profiles = get_field('profile',$postId);
                        //die(var_dump($profiles[0]['profiel']));
                        
                        
                        $employerID = get_field('user', $postId);
                        
                        $employer = get_user_by('ID', $employerID);
                        
                        $office = get_field('kantoor_acf',$postId);//get_post_meta($postId, 'office', true);
                        $docLang = get_field('language_doc_id',$postId);
                        $webSite = get_field('website',$postId);
                        
                        $online = get_field('online',$postId);
                        
                        $online_encodage_prestaties = $online['online_encodage_prestaties'];
                        $online_invoices_mail = $online['online_invoices_mail'];
                        $online_contracts_mail = $online['online_contracts_mail'];
                        $online_prestform_mail = $online['online_prestform_mail'];
                        $online_encodage_mail = $online['online_encodage_mail'];
                        
                        $online_encodage_enabled = $online['online_encodage_enabled'];
                        $online_invoices_enabled = $online['online_invoices_enabled'];
                        $online_contracts_enabled = $online['online_contracts_enabled'];
                        $online_prestform_enabled = $online['online_prestform_enabled'];
                        $paritair_comite_arbeiders = get_field('paritair_comite_arbeiders',$postId);
                        $pcNumber_arbeiders = get_post_meta($paritair_comite_arbeiders, 'comite_id', true);
                        $paritair_comite_bedienden = get_field('paritair_comite_bedienden',$postId);
                        $pcNumber_bedienden = get_post_meta($paritair_comite_bedienden, 'comite_id', true);
                        
                        
                        //facturatie
                        $invoice_period = !empty(get_field('invoice_period',$postId)) ? get_field('invoice_period',$postId) : 'month';
                        $paycondition_days = !empty(get_field('paycondition_days',$postId)) ? get_field('paycondition_days',$postId) : '7';
                        $vat = !empty(get_field('vat',$postId)) ? get_field('vat',$postId) : '21';
                        $vat_liable = !empty(get_field('vat_liable',$postId)) ? 'true' : 'false';
                        $invoice_split_statute = !empty(get_field('invoice_split_statute',$postId)) ? '1' : '0';
                        $invoice_split_dept = !empty(get_field('invoice_split_dept',$postId)) ? '1' : '0';
                        $invoice_split_person = !empty(get_field('invoice_split_person',$postId)) ? '1' : '0';
                        $subtotals_costcenter = !empty(get_field('subtotals_costcenter',$postId)) ? '1' : '0';
                        $facturatie_adres = get_field('facturatie_adres',$postId);
                        
                        
                        
                        
                        
                        $user = wp_get_current_user();
                        
                        $args = array(
                            'post_type' => 'absoluteyouemployee',
                            'posts_per_page' => 1,
                            'order' => 'DESC',
                            'meta_query' => array(
                                array(
                                    'key' => 'user',
                                    'value' => $user->ID,
                                    'compare' => '=',
                                    )
                                    )
                                );
                                
                                $salespersonpostquery = new WP_Query($args);
                                
                                $salespersonPost = null;
                                
                                $salesperson = '';
                                
                                if ($salespersonpostquery->have_posts()) :
                                    while ($salespersonpostquery->have_posts()) :
                                        $salespersonpostquery->the_post();
                                        
                                        $salespersonPost =  $salespersonpostquery->post->ID;
                                        $salesperson = get_field('user_be_bright',$salespersonPost);
                                        
                                    endwhile;
                                endif;
                                
                                wp_reset_postdata();
                                
                                
                                
                                $apiData = array(
                                    'enterprise_id' => '0',
                                    'search_name' => get_field('bedrijfsnaam_acf',$postId), //get_post_meta($postId, 'bedrijfsnaam', true),
                                    'gen_name' => get_field('bedrijfsnaam_acf',$postId),//get_post_meta($postId, 'bedrijfsnaam', true),
                                    'office_id' => !empty($office) ? $office : OFFICE_ID,
                                    'vatnumber' => get_field('btwnummer_acf',$postId),//get_post_meta($postId, 'btw_nummer', true),
                                    'vatcountry_iso' => get_field('isoland_acf',$postId), //get_post_meta($postId, 'iso_land', true),
                                    'street' => $facturatie_adres['inv_street'],//get_post_meta($postId, 'street', true),
                                    'street_nr' => $facturatie_adres['inv_street_nr'],//get_post_meta($postId, 'street_nr', true),
                                    'postal_code' => $facturatie_adres['inv_postal_code'],//get_post_meta($postId, 'postal_code', true),
                                    'city' => $facturatie_adres['inv_city'],//get_post_meta($postId, 'city', true),
                                    'country_iso' => $facturatie_adres['inv_country_iso'],//strtolower(get_post_meta($postId, 'country', true)),
                                    'bus' => $facturatie_adres['bus'],
                                    'extref' => $postId,
                                    'phone' => get_field('telefoonnummer_acf',$postId),//get_post_meta($postId, 'telefoonnummer', true),
                                    'mail' => get_field('e_mailadres_acf',$postId),//get_post_meta($postId, 'e_mailadres', true),
                                    'vat_vies_check' => '1',
                                    'is_customer' => '1',
                                    'language_doc_id' => !empty($docLang) ? $docLang : 'nl',
                                    'user_consulent_id' => !empty($salesperson) ? $salesperson : 4,
                                    'website' => !empty($webSite) ? $webSite : '',
                                    'online_encodage_mail' => !empty($online_encodage_mail) ? $online_encodage_mail : '',
                                    'online_invoices_mail' => !empty($online_invoices_mail) ? $online_invoices_mail : '',
                                    'online_contracts_mail' => !empty($online_contracts_mail) ? $online_contracts_mail : '',
                                    'online_prestform_mail' => !empty($online_prestform_mail) ? $online_prestform_mail : '',
                                    //'online_encodage_mail' => !empty($online_encodage_mail) ? $online_encodage_mail : '',
                                    'online_encodage_enabled' => !empty($online_encodage_enabled) ? '1' : '0',
                                    'online_invoices_enabled' => !empty($online_invoices_enabled) ? '1' : '0',
                                    'online_contracts_enabled' => !empty($online_contracts_enabled) ? '1' : '0',
                                    'online_prestform_enabled' => !empty($online_prestform_enabled) ? '1' : '0',
                                    'compar_blue_id' => !empty($pcNumber_arbeiders) ? $pcNumber_arbeiders : '',
                                    'compar_white_id' => !empty($pcNumber_bedienden) ? $pcNumber_bedienden : '',
                                    'paycondition_endofmonth' => '0',
                                    'invoice_period' => $invoice_period,
                                    'paycondition_days' => $paycondition_days,
                                    'default_paycondition' =>$paycondition_days,
                                    'vat' => $vat,
                                    'vat_liable' => $vat_liable,
                                    'invoice_split_statute' => $invoice_split_statute,
                                    'invoice_split_dept' => $invoice_split_dept,
                                    'invoice_split_person' => $invoice_split_person,
                                    'subtotals_costcenter' => $subtotals_costcenter,
                                    'inv_street' => $facturatie_adres['inv_street'],
                                    'inv_street_nr' => $facturatie_adres['inv_street_nr'],
                                    'inv_postal_code' => $facturatie_adres['inv_postal_code'],
                                    'inv_city' => $facturatie_adres['inv_city'],
                                    'inv_country_iso' => $facturatie_adres['inv_country_iso'],
                                    'domicile_payment' => !empty($facturatie_adres['domicile_payment']) ? '1' : '0',
                                    
                                    
                                );
                                
                                //die(json_encode($apiData));
                                //Create this fields (JPG)
                                $apiData['isoland'] = $apiData['vatcountry_iso'] ;
                                $apiData['btwnummer'] = $apiData['vatnumber'] ;
                                
                                $employerBeBright = verifyEmployeerbyBtwBeBrightPHP($apiData['isoland'] . $apiData['btwnummer']);
                                
                                /*var_dump($employerBeBright['enterprise']);
                                die();*/
                                
                                if (empty($employerBeBright)) {
                                    
                                    $path = "enterprise/addEnterprise";
                                } else {
                                    $path = "enterprise/updateEnterprise";
                                    $apiData['enterprise_id'] = $employerBeBright['enterprise']['enterprise_id'];
                                }
                                
                                $params = array('api_access_token' => API_TOKEN, 'api_version' => API_VERSION, 'enterprise' => json_encode($apiData, true));
                                
                                $response = executeCurl($path, $params);
                                
                                $apiResponse = json_decode($response, true);
                                
                                
                                
                                if (!isset($apiResponse['enterprise_id'])) {
                                    
                                    die($response);
                                }
                                
                                $field = get_user_meta($employerID, 'enterprise_id', true);
                                
                                
                                if (isset($field) || !empty($field)) {
                                    update_user_meta($employerID, 'enterprise_id', $apiResponse['enterprise_id']);
                                } else {
                                    add_user_meta($employerID, 'enterprise_id', $apiResponse['enterprise_id']);
                                }
                                
                                $field2 = get_user_meta($employerID, 'btw', true);
                                
                                //delete_user_meta($employerID, 'btw');
                                if (isset($field2) || !empty($field2)) {
                                update_user_meta($employerID, 'btw', $apiData['isoland'] . $apiData['btwnummer'] /*get_post_meta($postId, 'iso_land', true).get_post_meta($postId, 'btw_nummer', true)*/);
                            } else {
                            add_user_meta($employerID, 'btw', $apiData['isoland'] . $apiData['btwnummer'] /*get_post_meta($postId, 'iso_land', true).get_post_meta($postId, 'btw_nummer', true)*/);
                        }
                        
                        $registered =  get_post_meta($postId, 'bebright_registered', true);
                        if (isset($registered) || !empty($registered)) {
                            update_post_meta($postId,'bebright_registered','1');
                        } else {
                            add_post_meta($postId,'bebright_registered','1');
                        }
                        
                        if (empty($employerBeBright)){
                            //sendEmployerRegisterEmail($employer);
                        }
                        
                        
                        //departments
                        $rows = get_field('vestigingen',$postId);
                        if( $rows ) {
                            
                            //enterprise/getEnterpriseDepartments
                            $path3v = "enterprise/getEnterpriseDepartments";
                            
                            $params3v = array('api_access_token' => API_TOKEN, 'api_version' => API_VERSION, 'enterprise_id' => $apiResponse['enterprise_id']);
                            
                            $response3v = executeCurl($path3v, $params3v);
                            
                            $apiResponse3v = json_decode($response3v, true);
                            
                            
                            foreach( $rows as $row ) {
                                
                                $department = array(
                                    'name' => $row['name'],
                                    'tav' => $row['tav'],
                                    'info' => $row['info'],
                                    'street' => $row['street'],
                                    'street_nr' => $row['street_nr'],
                                    'bus' => $row['bus'],
                                    'city' => $row['city'],
                                    'country_iso' => $row['country_iso'],
                                    'lat' => $row['lat'],
                                    'lng' => $row['lng'],
                                    'postal_code' => $row['postal_code'],
                                    
                                    //'extref' => get_sub_field('title'),
                                );
                                
                                if ($path == "enterprise/addEnterprise"){
                                    
                                    
                                    $path2 = "enterprise/addEnterpriseDepartment";
                                    
                                    $params2 = array('api_access_token' => API_TOKEN, 'api_version' => API_VERSION, 'department' => json_encode($department, true), 'enterprise_id' => $apiResponse['enterprise_id']);
                                    
                                    $response2 = executeCurl($path2, $params2);
                                    
                                    $apiResponse2 = json_decode($response2, true);
                                    
                                    if (!isset($apiResponse2['department_id'])) {
                                        
                                        die($response2);
                                    }
                                }
                                else{
                                    //if not is addenterprise 
                                    //search the departments and update these in bbright
                                    
                                    
                                    
                                    //get the departments from bebright and update these
                                    
                                    
                                    //if there are not departments then add this
                                    if (!isset($apiResponse3v['departments']) || count($apiResponse3v['departments'])==0){
                                        
                                        $path4 = "enterprise/addEnterpriseDepartment";
                                        
                                        $params4 = array('api_access_token' => API_TOKEN, 'api_version' => API_VERSION, 'department' => json_encode($department, true), 'enterprise_id' => $apiResponse['enterprise_id']);
                                        
                                        $response4 = executeCurl($path4, $params4);
                                        
                                        $apiResponse4 = json_decode($response4, true);
                                        
                                        if (!isset($apiResponse4['department_id'])) {
                                            
                                            die($response4);
                                        }
                                        
                                        
                                        
                                    }
                                    else{
                                        
                                        //find the department row in apiresponse2['departments'] by name
                                        //buscar la forma de agregar el id del departamento cuando se agregue en bebright al repeater
                                        $exist = false;
                                        foreach ($apiResponse3v['departments'] as $key => $dep) {
                                            
                                            if ($department['name'] == $dep['name']){
                                                
                                                $department['department_id'] = $dep['department_id'];
                                                $exist = true;
                                                break;
                                                
                                                
                                            }
                                            
                                        }
                                        
                                        if (!$exist){
                                            
                                            $path4 = "enterprise/addEnterpriseDepartment";
                                            
                                            $params4 = array('api_access_token' => API_TOKEN, 'api_version' => API_VERSION, 'department' => json_encode($department, true), 'enterprise_id' => $apiResponse['enterprise_id']);
                                            
                                            $response4 = executeCurl($path4, $params4);
                                            
                                            $apiResponse4 = json_decode($response4, true);
                                            
                                            if (!isset($apiResponse4['department_id'])) {
                                                
                                                die($response4);
                                            }
                                            
                                        }
                                        else{
                                            $path5 = "enterprise/updateEnterpriseDepartment";
                                            
                                            $params5 = array('api_access_token' => API_TOKEN, 'api_version' => API_VERSION, 'department' => json_encode($department, true), 'enterprise_id' => $apiResponse['enterprise_id']);
                                            
                                            $response5 = executeCurl($path5, $params5);
                                            
                                            $apiResponse5 = json_decode($response5, true);
                                            
                                            if (!isset($apiResponse5['department_id'])) {
                                                
                                                die($response5);
                                            }
                                            
                                        }
                                        
                                        
                                    }
                                    
                                    
                                }
                            }
                            
                        }
                        
                        
                        //contacts
                        
                        
                        $contacts = get_field('contacts',$postId);
                        if( $contacts ) {
                            
                            $path3 = "contact/getContacts";
                            
                            $params3 = array('api_access_token' => API_TOKEN, 'api_version' => API_VERSION, 'enterprise_id' => $apiResponse['enterprise_id']);
                            
                            $response3 = executeCurl($path3, $params3);
                            
                            $apiResponse3 = json_decode($response3, true);
                            
                            
                            foreach( $contacts as $key => $row ) {
                                
                                $index = $key + 1;
                                
                                $contact = array(
                                    'contact_id' => '0',
                                    'enterprise_id' => $apiResponse['enterprise_id'],
                                    'firstname' => $row['firstname'],
                                    'lastname' => $row['lastname'],
                                    'phone' => $row['phone'],
                                    'mobile' => $row['mobile'],
                                    'mail' => $row['mail'],
                                    'language' => $row['language'],
                                    'function' => $row['function'],
                                    'is_decision_maker' => !empty($row['is_decision_maker'])?'true':'false',
                                    'is_influencer' => !empty($row['is_influencer'])?'true':'false',
                                    'allow_contact' => !empty($row['allow_contact'])?'true':'false',
                                    'allow_email' => !empty($row['allow_email'])?'true':'false',
                                    'birth_date' => $row['birth_date']
                                    
                                );
                                
                                
                                
                                if ($path == "enterprise/addEnterprise"){
                                    
                                    
                                    $path2 = "contact/addContact";
                                    
                                    $params2 = array('api_access_token' => API_TOKEN, 'api_version' => API_VERSION, 'contact' => json_encode($contact, true), 'enterprise_id' => $apiResponse['enterprise_id']);
                                    
                                    $response2 = executeCurl($path2, $params2);
                                    
                                    $apiResponse2 = json_decode($response2, true);
                                    
                                    if (!isset($apiResponse2['contact_id'])) {
                                        
                                        die($response2);
                                    }
                                    
                                    update_sub_field( array('contacts', $index  , 'id_bbright'), $apiResponse2['contact_id'] ,$postId );
                                }
                                else{
                                    
                                    if ( isset($apiResponse3['contacts']) && count($apiResponse3['contacts'])==0){
                                        
                                        
                                        
                                        $path4 = "contact/addContact";
                                        
                                        $params4 = array('api_access_token' => API_TOKEN, 'api_version' => API_VERSION, 'contact' => json_encode($contact, true), 'enterprise_id' => $apiResponse['enterprise_id']);
                                        
                                        $response4 = executeCurl($path4, $params4);
                                        
                                        $apiResponse4 = json_decode($response4, true);
                                        
                                        
                                        
                                        if (!isset($apiResponse4['contact_id'])) {
                                            
                                            die($response4);
                                        }
                                        
                                        update_sub_field( array('contacts', $index  , 'id_bbright'), $apiResponse4['contact_id'],$postId );
                                        
                                        
                                        
                                    }
                                    else{
                                        
                                        
                                        //update contact is not exist in API
                                        
                                        $exist = false;
                                        foreach ($apiResponse3['contacts'] as $key => $cont) {
                                            
                                            if ($row['id_bbright'] == $cont['contact_id']){
                                                $contact['contact_id'] = $cont['contact_id'];
                                                $exist = true;
                                                break;
                                            }
                                            
                                        }
                                        
                                        if (!$exist){
                                            $path4 = "contact/addContact";
                                            
                                            $params4 = array('api_access_token' => API_TOKEN, 'api_version' => API_VERSION, 'contact' => json_encode($contact, true), 'enterprise_id' => $apiResponse['enterprise_id']);
                                            
                                            $response4 = executeCurl($path4, $params4);
                                            
                                            $apiResponse4 = json_decode($response4, true);
                                            
                                            
                                            
                                            if (!isset($apiResponse4['contact_id'])) {
                                                
                                                die($response4);
                                            }
                                            
                                            update_sub_field( array('contacts', $index  , 'id_bbright'), $apiResponse4['contact_id'],$postId );
                                            
                                        }
                                        
                                        
                                    }
                                    
                                    
                                }
                                
                            }
                            
                        }
                        
                        
                        //commercial profiles
                        $profiles = get_field('profile',$postId);
                        if( $profiles ) {
                            
                            $path3 = "commprofile/getProfilesByEnterpriseId";
                            
                            $params3 = array('api_access_token' => API_TOKEN, 'api_version' => API_VERSION, 'enterprise_id' => $apiResponse['enterprise_id']);
                            
                            $response3 = executeCurl($path3, $params3);
                            
                            $apiResponse3CommercialProfiles = json_decode($response3, true);
                            
                            // die(var_dump($apiResponse3CommercialProfiles['comm_profiles']));
                            
                            foreach( $profiles as $key => $row ) {
                                
                                $index = $key + 1;
                                
                                $date = date_create_from_format('d/m/Y',$row['profile_data']['profile_start']);
                                
                                
                                $pcNumber_comm = get_post_meta($row['profile_data']['parcom_id'], 'comite_id', true);
                                
                                
                                
                                $profile = array(
                                    'name' => $row['profiel']['profile_name'],
                                    'statute_id' => $row['profiel']['statute']['value'],
                                    'parcom_id' => $pcNumber_comm,
                                    'coef_selection' => $row['profile_data']['coef_selection'],
                                    'coef_payroll' => $row['profile_data']['coef_payroll'],
                                    'coef_reduced' => $row['profile_data']['coef_reduced'],
                                    'coef_transport' => $row['profile_data']['coef_transport'],
                                    'coef_holiday' => $row['profile_data']['coef_holiday'],
                                    'coef_holiday_payroll' => $row['profile_data']['coef_feestdag_payroll'],
                                    'coef_holiday_reduced' => $row['profile_data']['coef_holiday_reduced'],
                                    'coef_mtc' => $row['profile_data']['coef_mtc'],
                                    'coef_eco' => $row['profile_data']['coef_eco'],
                                    'dimona_cost' => $row['profile_data']['dimona_cost'],
                                    'dimona_invoice' => !empty($row['profile_data']['dimona_invoice']) ? '1' : '0',
                                    'holiday_invoice' => !empty($row['profile_data']['holiday_invoice']) ? '0' : '1',
                                    'transport_invoice' => !empty($row['profile_data']['transport_invoice']) ? '1' : '0',
                                    'ecocheque_invoice' => !empty($row['profile_data']['ecocheque_invoice']) ? '1' : '0',
                                    'profile_start' => date_format($date, "d/m/Y")
                                    
                                    
                                    //'extref' => get_sub_field('title'),
                                );
                                
                                
                                
                                
                                
                                
                                if ($path == "enterprise/addEnterprise"){
                                    
                                    
                                    $path2 = "enterprise/addEnterpriseCommProfile";
                                    
                                    $params2 = array('api_access_token' => API_TOKEN, 'api_version' => API_VERSION, 'comm_profile' => json_encode($profile, true), 'enterprise_id' => $apiResponse['enterprise_id']);
                                    
                                    $response2 = executeCurl($path2, $params2);
                                    
                                    $apiResponse2 = json_decode($response2, true);
                                    
                                    if (!isset($apiResponse2['comm_profile_id'])) {
                                        
                                        die($response2);
                                    }
                                    
                                    update_sub_field( array('profile', $index  , 'profile_id'), $apiResponse2['comm_profile_id'] ,$postId );
                                }
                                else{
                                    //if not is addenterprise 
                                    //search the departments and update these in bbright
                                    
                                    
                                    
                                    
                                    //check if exists commercial profiles on bbright
                                    
                                    //if there are not departments then add this
                                    if (!isset($apiResponse3CommercialProfiles['comm_profiles']) || count($apiResponse3CommercialProfiles['comm_profiles'])==0){
                                        
                                        $path444 = "enterprise/addEnterpriseCommProfile";
                                        
                                        $params444 = array('api_access_token' => API_TOKEN, 'api_version' => API_VERSION, 'comm_profile' => json_encode($profile, true), 'enterprise_id' => $apiResponse['enterprise_id']);
                                        
                                        $response444 = executeCurl($path444, $params444);
                                        
                                        $apiResponse444 = json_decode($response444, true);
                                        
                                        if (!isset($apiResponse444['comm_profile_id'])) {
                                            
                                            die($response444);
                                        }
                                        
                                        
                                        
                                        update_sub_field( array('profile', $index  , 'profile_id'), $apiResponse444['comm_profile_id'] ,$postId );
                                        
                                        
                                        
                                    }
                                    else{
                                        
                                        //find the department row in apiresponse2['departments'] by name
                                        //buscar la forma de agregar el id del departamento cuando se agregue en bebright al repeater
                                        $exist = false;
                                        foreach ($apiResponse3CommercialProfiles['comm_profiles'] as $key => $prof) {
                                            
                                            if ($row['profile_id'] == $prof['comm_profile_id']){
                                                
                                                $profile['comm_profile_id'] = $prof['comm_profile_id'];
                                                $profile['commprofile_id'] = $prof['comm_profile_id'];
                                                $exist = true;
                                                break;
                                                
                                                
                                            }
                                            
                                        }
                                        
                                        
                                        
                                        if ($exist == false){
                                            
                                            $path4 = "enterprise/addEnterpriseCommProfile";
                                            
                                            $params4 = array('api_access_token' => API_TOKEN, 'api_version' => API_VERSION, 'comm_profile' => json_encode($profile, true), 'enterprise_id' => $apiResponse['enterprise_id']);
                                            
                                            $response4 = executeCurl($path4, $params4);
                                            
                                            $apiResponse4 = json_decode($response4, true);
                                            
                                            if (!isset($apiResponse4['comm_profile_id'])) {
                                                
                                                die($response4);
                                            }
                                            
                                            update_sub_field( array('profile', $index  , 'profile_id'), $apiResponse4['comm_profile_id'] ,$postId );
                                            
                                        }
                                        else{
                                            
                                            $path5 = "enterprise/updateEnterpriseCommProfile";
                                            
                                            $params5 = array('api_access_token' => API_TOKEN, 'api_version' => API_VERSION, 'comm_profile' => json_encode($profile, true), 'enterprise_id' => $apiResponse['enterprise_id']);
                                            
                                            $response5 = executeCurl($path5, $params5);
                                            
                                            $apiResponse5 = json_decode($response5, true);
                                            
                                            if (!isset($apiResponse5['comm_profile_id'])) {
                                                
                                                die($response5);
                                            }
                                            
                                        }
                                        
                                        
                                        
                                        
                                    }
                                    
                                    
                                }
                                
                                
                            }//end foreach
                            
                        }
                        
                        
                        //loonprofiles
                        //$loonprofiles = get_field('loonprofiles',$postId);
                        if( $profiles ) {
                            
                            $path33 = "payrollprofile/getProfilesByEnterpriseId";
                            
                            $params33 = array('api_access_token' => API_TOKEN, 'api_version' => API_VERSION, 'enterprise_id' => $apiResponse['enterprise_id']);
                            
                            $response33 = executeCurl($path33, $params33);
                            
                            $apiResponse33LoonProfiles = json_decode($response33, true);
                            
                            //die(var_dump($apiResponse33LoonProfiles['payroll_profiles'] ));
                            
                            foreach( $profiles as $key2 => $row ) {
                                
                                $index2 = $key2 + 1;
                                
                                $pcNumber_comm = get_post_meta($row['profile_data']['parcom_id'], 'comite_id', true);
                                
                                $date = date_create_from_format('d/m/Y',$row['profile_data']['profile_start']);   
                                
                                
                                
                                
                                
                                $loonprofile = array(
                                    'name' => $row['profiel']['profile_name'],
                                    'statute_id' => $row['profiel']['statute']['value'],
                                    'parcom_id' => $pcNumber_comm,
                                    'is_recup_paid' => !empty($row['profile_data']['is_recup_paid'])?'1' : '0',
                                    'mtc_total' => $row['profile_data']['mtc_total'] == '' ? '0' : $row['profile_data']['mtc_total'],
                                    'mtc_employer' => $row['profile_data']['mtc_employer'] == '' ? '0' : $row['profile_data']['mtc_employer'],
                                    'mtc_hours' => $row['profile_data']['mtc_hours'] == '' ? '0' : $row['profile_data']['mtc_hours'],
                                    'profile_start' => date_format($date, "d/m/Y")
                                );
                                
                                if ($path == "enterprise/addEnterprise"){
                                    
                                    
                                    $pathL = "enterprise/addEnterprisePayrollProfile";
                                    
                                    $paramsL = array('api_access_token' => API_TOKEN, 'api_version' => API_VERSION, 'payroll_profile' => json_encode($loonprofile, true), 'enterprise_id' => $apiResponse['enterprise_id']);
                                    
                                    $responseL = executeCurl($pathL, $paramsL);
                                    
                                    $apiResponseL = json_decode($responseL, true);
                                    
                                    if (!isset($apiResponseL['payroll_profile_id'])) {
                                        
                                        die($responseL);
                                    }
                                    
                                    //update_field( array('profiles', $index2  , 'profile_id_loon'), $apiResponseL['payroll_profile_id'] ,$postId );
                                    update_sub_field( array('profile', $index2  , 'profile_id_loon'), $apiResponseL['payroll_profile_id'] ,$postId );
                                }
                                else{
                                    //if not is addenterprise 
                                    //search the departments and update these in bbright
                                    
                                    
                                    
                                    //get the departments from bebright and update these
                                    
                                    //enterprise/getEnterpriseDepartments
                                    
                                    //compare if exists loonprofiles on bbright
                                    //if there are not loonprofiles then add this
                                    if (!isset($apiResponse33LoonProfiles['payroll_profiles']) || count($apiResponse33LoonProfiles['payroll_profiles'])==0){
                                        
                                        $path44 = "enterprise/addEnterprisePayrollProfile";
                                        
                                        $params44 = array('api_access_token' => API_TOKEN, 'api_version' => API_VERSION, 'payroll_profile' => json_encode($loonprofile, true), 'enterprise_id' => $apiResponse['enterprise_id']);
                                        
                                        $response44 = executeCurl($path44, $params44);
                                        
                                        $apiResponse44 = json_decode($response44, true);
                                        
                                        if (!isset($apiResponse44['payroll_profile_id'])) {
                                            
                                            die($response44);
                                        }
                                        
                                        //update_sub_field( array('loonprofiles', $index2  , 'profile_id'), $apiResponse44['payroll_profile_id'] ,$postId );
                                        update_sub_field( array('profile', $index2  , 'profile_id_loon'), $apiResponse44['payroll_profile_id'] ,$postId );
                                        
                                        
                                    }
                                    else{
                                        
                                        //find the department row in apiresponse2['departments'] by name
                                        //buscar la forma de agregar el id del departamento cuando se agregue en bebright al repeater
                                        $existl = false;
                                        foreach ($apiResponse33LoonProfiles['payroll_profiles'] as $key => $prof) {
                                            //die(var_dump($row['profile_id_loon'] == $prof['payrollprofile_id']));
                                            if ($row['profile_id_loon'] == $prof['payrollprofile_id']){
                                                
                                                $loonprofile['payrollprofile_id'] = $prof['payrollprofile_id'];
                                                $loonprofile['payroll_profile_id'] = $prof['payrollprofile_id'];
                                                
                                                $existl = true;
                                                break;
                                                
                                                
                                            }
                                            
                                        }
                                        
                                        
                                        if ($existl == false){
                                            
                                            $path44 = "enterprise/addEnterprisePayrollProfile";
                                            
                                            $params44 = array('api_access_token' => API_TOKEN, 'api_version' => API_VERSION, 'payroll_profile' => json_encode($loonprofile, true), 'enterprise_id' => $apiResponse['enterprise_id']);
                                            
                                            $response44 = executeCurl($path44, $params44);
                                            
                                            $apiResponse44 = json_decode($response44, true);
                                            
                                            if (!isset($apiResponse44['payroll_profile_id'])) {
                                                
                                                die($response44);
                                            }
                                            
                                            //update_sub_field( array('loonprofiles', $index2  , 'profile_id'), $apiResponse44['payroll_profile_id'] ,$postId );
                                            update_sub_field( array('profile', $index2  , 'profile_id_loon'), $apiResponse44['payroll_profile_id'] ,$postId );
                                            
                                            
                                            
                                        }
                                        else{
                                            
                                            $path55 = "enterprise/updateEnterprisePayrollProfile";
                                            
                                            $params55 = array('api_access_token' => API_TOKEN, 'api_version' => API_VERSION, 'payroll_profile' => json_encode($loonprofile, true), 'enterprise_id' => $apiResponse['enterprise_id']);
                                            
                                            $response55 = executeCurl($path55, $params55);
                                            
                                            $apiResponse55 = json_decode($response55, true);
                                            
                                            if (!isset($apiResponse55['payroll_profile_id'])) {
                                                
                                                die($response55);
                                            }
                                            
                                        }
                                        
                                        
                                        
                                        
                                    }
                                    
                                    
                                }
                            }
                        }
                        
                        
                        die('success');
                    }
                    
                    add_action('wp_ajax_RegisterEmployerBeBright', 'RegisterEmployerBeBright');
                    add_action('wp_ajax_nopriv_RegisterEmployerBeBright', 'RegisterEmployerBeBright');
                    
                    
                    function sendEmployerRegisterEmailBackend(){
                        
                        $postId = $_POST['postId'];
                        
                        $employerID = get_field('user', $postId);
                        
                        $employer = get_user_by('ID', $employerID);
                        
                        
                        if (sendEmployerRegisterEmail($employer)){
                            die('success');
                        }
                        die('Fout bij het verzenden van bericht');
                        
                        
                    }
                    
                    add_action('wp_ajax_sendEmployerRegisterEmailBackend', 'sendEmployerRegisterEmailBackend');
                    add_action('wp_ajax_nopriv_sendEmployerRegisterEmailBackend', 'sendEmployerRegisterEmailBackend');
                    
                    
                    function getComiteesEmployer()
                    {
                        
                        $employer = wp_get_current_user();
                        
                        $responsec = [];
                        
                        //get the employer post and the comitees
                        
                        $args = array(
                            'post_type' => 'contract',
                            'posts_per_page' => 1,
                            'order' => 'DESC',
                            'meta_query' => array(
                                'relation' => 'AND',
                                array(
                                    'key' => 'user',
                                    'value' => (string) $employer->ID,
                                    'compare' => '=',
                                    )
                                    )
                                );
                                
                                $employerContract = new WP_Query($args);
                                
                                
                                
                                if ($employerContract->have_posts()) :
                                    while ($employerContract->have_posts()) :
                                        $employerContract->the_post();
                                        
                                        
                                        $employerPost =  $employerContract->post->ID;
                                        
                                        
                                        
                                        $comitees_a =[];
                                        $comitees_a = get_post_meta($employerPost, 'paritair_comite_arbeiders', true);
                                        
                                        $comitees_b =[];
                                        $comitees_b = get_post_meta($employerPost, 'paritair_comite_bedienden', true);
                                        
                                        
                                        $field = get_field_object('another_functions',$comitees_a);
                                        $choices = $field['choices'];
                                        
                                        $c = array();
                                        $c['committee_id'] = get_post_meta($comitees_a, 'comite_id', true);
                                        $c['name'] = str_replace('&#8211;','-', html_entity_decode( get_the_title($comitees_a), ENT_QUOTES, 'UTF-8' ));
                                        $c['salary'] = get_post_meta($comitees_a, 'salary', true);
                                        $c['categories'] = get_field('categorieen',$comitees_a);
                                        $c['functions'] = get_field('functions_pc',$comitees_a);
                                        $c['functions'] = empty($c['functions']) ? [] : $c['functions'];
                                        $anotherFunctions = get_field('another_functions',$comitees_a);
                                        foreach ($anotherFunctions as $key => $value) {
                                            array_push($c['functions'],$value);
                                        }        
                                        $c['another_functions'] = !empty($choices) ? $choices : [];
                                        array_push($responsec, $c);
                                        
                                        
                                        
                                        $c2 = array();
                                        $c2['committee_id'] = get_post_meta($comitees_b, 'comite_id', true);
                                        $c2['name'] =str_replace('&#8211;','-', html_entity_decode( get_the_title($comitees_b), ENT_QUOTES, 'UTF-8' ));
                                        $c2['salary'] = get_post_meta($comitees_b, 'salary', true);
                                        $c2['categories'] = get_field('categorieen',$comitees_b);
                                        $c2['functions'] = get_field('functions_pc',$comitees_b);
                                        $c2['functions'] = empty($c2['functions']) ? [] : $c2['functions'];
                                        $anotherFunctions2 = get_field('another_functions',$comitees_b);
                                        foreach ($anotherFunctions2 as $key => $value) {
                                            array_push($c2['functions'],$value);
                                        }  
                                        $c2['another_functions'] = !empty($choices) ? $choices : [];
                                        array_merge($c2['functions'],$anotherFunctions2);
                                        
                                        
                                        array_push($responsec, $c2);
                                        
                                        
                                        
                                    endwhile;
                                endif;
                                
                                wp_reset_postdata();
                                
                                
                                
                                die(json_encode($responsec));
                            }
                            
                            add_action('wp_ajax_getComiteesEmployer', 'getComiteesEmployer');
                            add_action('wp_ajax_nopriv_getComiteesEmployer', 'getComiteesEmployer');
                            
                            function getOffices()
                            {
                                $js = isset($_POST['js']) ? true : false;
                                $params = array('api_access_token' => API_TOKEN, 'api_version' => API_VERSION);
                                $path = 'office/getOffices';
                                $response = executeCurl($path, $params);
                                
                                if ($js) {
                                    die($response);
                                }
                                
                                return $response;
                            }
                            
                            add_action('wp_ajax_getOffices', 'getOffices');
                            add_action('wp_ajax_nopriv_getOffices', 'getOffices');
                            
                            