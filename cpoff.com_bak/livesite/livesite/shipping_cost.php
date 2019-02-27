<?php

/**
 *
 * liveSite - Enterprise Website Platform
 *
 * @author      Camelback Web Architects
 * @link        https://livesite.com
 * @copyright   2001-2017 Camelback Consulting, Inc.
 * @license     https://opensource.org/licenses/mit-license.html MIT License
 *
 */

// Various functions for determining shipping cost & rates.


function update_shipping_cost_for_ship_to($ship_to_id) {

    // get info for this ship to
    $query =
        "SELECT
            ship_tos.shipping_cost,
            ship_tos.original_shipping_cost,
            ship_tos.offer_id,
            ship_tos.state,
            ship_tos.zip_code,
            shipping_methods.service as shipping_method_service,
            shipping_methods.base_rate as shipping_method_base_rate,
            shipping_methods.variable_base_rate AS shipping_method_variable_base_rate,
            shipping_methods.base_rate_2 AS shipping_method_base_rate_2,
            shipping_methods.base_rate_2_subtotal AS shipping_method_base_rate_2_subtotal,
            shipping_methods.base_rate_3 AS shipping_method_base_rate_3,
            shipping_methods.base_rate_3_subtotal AS shipping_method_base_rate_3_subtotal,
            shipping_methods.base_rate_4 AS shipping_method_base_rate_4,
            shipping_methods.base_rate_4_subtotal AS shipping_method_base_rate_4_subtotal,
            shipping_methods.primary_weight_rate AS shipping_method_primary_weight_rate,
            shipping_methods.primary_weight_rate_first_item_excluded AS shipping_method_primary_weight_rate_first_item_excluded,
            shipping_methods.secondary_weight_rate AS shipping_method_secondary_weight_rate,
            shipping_methods.secondary_weight_rate_first_item_excluded AS shipping_method_secondary_weight_rate_first_item_excluded,
            shipping_methods.item_rate AS shipping_method_item_rate,
            shipping_methods.item_rate_first_item_excluded AS shipping_method_item_rate_first_item_excluded,
            zones.base_rate as zone_base_rate,
            zones.primary_weight_rate AS zone_primary_weight_rate,
            zones.secondary_weight_rate AS zone_secondary_weight_rate,
            zones.item_rate AS zone_item_rate
        FROM ship_tos
        LEFT JOIN shipping_methods ON ship_tos.shipping_method_id = shipping_methods.id
        LEFT JOIN zones ON ship_tos.zone_id = zones.id
        WHERE ship_tos.id = '" . escape($ship_to_id) . "'";
    $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
    $row = mysqli_fetch_assoc($result);
    $old_shipping_cost = $row['shipping_cost'];
    $old_original_shipping_cost = $row['original_shipping_cost'];
    $offer_id = $row['offer_id'];
    $state = $row['state'];
    $zip_code = $row['zip_code'];
    $shipping_method_service = $row['shipping_method_service'];
    $shipping_method_base_rate = $row['shipping_method_base_rate'];
    $shipping_method_variable_base_rate = $row['shipping_method_variable_base_rate'];
    $shipping_method_base_rate_2 = $row['shipping_method_base_rate_2'];
    $shipping_method_base_rate_2_subtotal = $row['shipping_method_base_rate_2_subtotal'];
    $shipping_method_base_rate_3 = $row['shipping_method_base_rate_3'];
    $shipping_method_base_rate_3_subtotal = $row['shipping_method_base_rate_3_subtotal'];
    $shipping_method_base_rate_4 = $row['shipping_method_base_rate_4'];
    $shipping_method_base_rate_4_subtotal = $row['shipping_method_base_rate_4_subtotal'];
    $shipping_method_primary_weight_rate = $row['shipping_method_primary_weight_rate'];
    $shipping_method_primary_weight_rate_first_item_excluded = $row['shipping_method_primary_weight_rate_first_item_excluded'];
    $shipping_method_secondary_weight_rate = $row['shipping_method_secondary_weight_rate'];
    $shipping_method_secondary_weight_rate_first_item_excluded = $row['shipping_method_secondary_weight_rate_first_item_excluded'];
    $shipping_method_item_rate = $row['shipping_method_item_rate'];
    $shipping_method_item_rate_first_item_excluded = $row['shipping_method_item_rate_first_item_excluded'];
    $zone_base_rate = $row['zone_base_rate'];
    $zone_primary_weight_rate = $row['zone_primary_weight_rate'];
    $zone_secondary_weight_rate = $row['zone_secondary_weight_rate'];
    $zone_item_rate = $row['zone_item_rate'];

    // Get all order items for this ship to that do not have free shipping enabled
    // in order to calculate costs for each item.
    $order_items = db_items(
        "SELECT
            order_items.id,
            order_items.quantity,
            products.name,
            products.weight,
            products.primary_weight_points,
            products.secondary_weight_points,
            products.length,
            products.width,
            products.height,
            products.container_required,
            products.extra_shipping_cost
         FROM order_items
         LEFT JOIN products ON products.id = order_items.product_id
         WHERE
            (order_items.ship_to_id = '" . escape($ship_to_id) . "')
            AND (products.free_shipping = '0')
         ORDER BY order_items.id ASC");

    $shipping_cost = 0;

    $realtime_rate = get_shipping_realtime_rate(array(
        'ship_to_id' => $ship_to_id,
        'service' => $shipping_method_service,
        'state' => $state,
        'zip_code' => $zip_code,
        'items' => $order_items));

    // If there was an error getting the realtime rate, then this shipping method is not valid for
    // the recipient, so mark recipient incomplete, so customer will be forced to choose a different
    // shipping method.
    if ($realtime_rate === false) {
        db("UPDATE ship_tos SET complete = '0' WHERE id = '" . e($ship_to_id) . "'");
        return;
    }

    $shipping_cost += $realtime_rate;

    $shipping_cost += get_shipping_method_base_rate(array(
        'base_rate' => $shipping_method_base_rate,
        'variable_base_rate' => $shipping_method_variable_base_rate,
        'base_rate_2' => $shipping_method_base_rate_2,
        'base_rate_2_subtotal' => $shipping_method_base_rate_2_subtotal,
        'base_rate_3' => $shipping_method_base_rate_3,
        'base_rate_3_subtotal' => $shipping_method_base_rate_3_subtotal,
        'base_rate_4' => $shipping_method_base_rate_4,
        'base_rate_4_subtotal' => $shipping_method_base_rate_4_subtotal,
        'ship_to_id' => $ship_to_id));

    $shipping_cost += $zone_base_rate;

    // Loop through all items in cart for this recipient in order
    // to calculate shipping cost for each item.
    foreach ($order_items as $key => $order_item) {
        $shipping_method_primary_weight_rate_quantity = $order_item['quantity'];
        $shipping_method_secondary_weight_rate_quantity = $order_item['quantity'];
        $shipping_method_item_rate_quantity = $order_item['quantity'];

        // If this is the first order item then check if we need to reduce the quantity
        // in order to exclude the first item from shipping charges.
        if ($key == 0) {
            // If the first item should be excluded for the primary weight calculation
            // for the shipping method, then reduce the quantity by one.
            if ($shipping_method_primary_weight_rate_first_item_excluded == 1) {
                $shipping_method_primary_weight_rate_quantity--;
            }

            // If the first item should be excluded for the secondary weight calculation
            // for the shipping method, then reduce the quantity by one.
            if ($shipping_method_secondary_weight_rate_first_item_excluded == 1) {
                $shipping_method_secondary_weight_rate_quantity--;
            }

            // If the first item should be excluded for the item calculation
            // for the shipping method, then reduce the quantity by one.
            if ($shipping_method_item_rate_first_item_excluded == 1) {
                $shipping_method_item_rate_quantity--;
            }
        }

        $shipping_method_cost = ($order_item['primary_weight_points'] * $shipping_method_primary_weight_rate * $shipping_method_primary_weight_rate_quantity) + ($order_item['secondary_weight_points'] * $shipping_method_secondary_weight_rate * $shipping_method_secondary_weight_rate_quantity) + ($shipping_method_item_rate * $shipping_method_item_rate_quantity);
        $zone_cost = ($order_item['primary_weight_points'] * $zone_primary_weight_rate * $order_item['quantity']) + ($order_item['secondary_weight_points'] * $zone_secondary_weight_rate * $order_item['quantity']) + ($zone_item_rate * $order_item['quantity']);
        $extra_shipping_cost = $order_item['extra_shipping_cost'] * $order_item['quantity'];
        $shipping_cost_for_item = $shipping_method_cost + $zone_cost + $extra_shipping_cost;
        $shipping_cost = $shipping_cost + $shipping_cost_for_item;
        
        db("UPDATE order_items SET shipping = '" . round($shipping_cost_for_item / $order_item['quantity']) . "' WHERE id = '" . $order_item['id'] . "'");
    }
    
    // assume that there is not a shipping discount and therefore no original shipping cost, until we find out otherwise
    $original_shipping_cost = 0;
    
    // if there is a shipping discount, then calculate current discount shipping percentage and update shipping cost
    if ($offer_id != 0) {
        // if the old original shipping cost is greater than 0, then calculate discount shipping percentage (protection against division by 0)
        if ($old_original_shipping_cost > 0) {
            $discount_shipping_percentage = ($old_original_shipping_cost - $old_shipping_cost) / $old_original_shipping_cost * 100;
            
        // else the old original shipping cost is 0, so set discount shipping percentage to 0
        } else {
            $discount_shipping_percentage = 0;
        }
        
        // remember the original shipping cost, before it is updated
        $original_shipping_cost = $shipping_cost;
        
        // update shipping cost to contain discount
        $shipping_cost = $shipping_cost - ($shipping_cost * ($discount_shipping_percentage / 100));
    }
    
    // update shipping cost for ship to
    $query =
        "UPDATE ship_tos
        SET
            shipping_cost = '$shipping_cost',
            original_shipping_cost = '$original_shipping_cost'
        WHERE id = '" . escape($ship_to_id) . "'";
    $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
}

function get_shipping_realtime_rate($properties) {

    $ship_to_id = $properties['ship_to_id'];
    $service = $properties['service'];
    $state = $properties['state'];
    $zip_code = $properties['zip_code'];
    $items = $properties['items'];

    // If there is not a real-time rate carrier and service selected for this shipping method,
    // then return a real-time rate of zero.
    if (!$service) {
        return 0;
    }

    // If a ship to id was passed and a state or zip was not passed, then get info for recipient.
    if ($ship_to_id and (!$state or !$zip_code)) {

        $recipient = db_item(
            "SELECT
                state,
                zip_code
            FROM ship_tos
            WHERE id = '" . e($ship_to_id) . "'");
        
        $state = $recipient['state'];
        $zip_code = $recipient['zip_code'];

    }

    // Just get the first 5 digits of the zip code, because USPS does not support +4.
    // It appears that UPS might support +4, but we are just going to remove the +4
    // for UPS also, so the same zip code is used for both carriers.
    $zip_code = mb_substr($zip_code, 0, 5);

    // Prepare timestamp so we only get cached rates that are recent.
    $day_ago_timestamp = time() - 86400;

    // Get a random number between 1 and 100 in order to determine if we should delete old rates
    // from the db cache table.  There is a 1 in 100 chance that we will delete old rates each time
    // we calculate real-time rates for a recipient.  Old is considered more than a day old.

    $random_number = rand(1, 100);
    
    if ($random_number == 1) {
        db("DELETE FROM shipping_rates WHERE timestamp < '$day_ago_timestamp'");
    }

    // Loop through the items in order to prepare package items.  Package items are every single
    // item that needs to be considered when determing the rate of packaging.  This includes
    // all quantities for all items that do not get free shipping.

    $package_items = array();

    foreach ($items as $item) {

        // If this is a free-shipping item, then skip to next item.
        if ($item['free_shipping']) {
            continue;
        }

        // Add a package item for each quantity.
        for ($quantity = 1; $quantity <= $item['quantity']; $quantity++) { 
            $package_items[] = $item;
        }

    }

    // If there are no items to package, then return zero.
    if (!$package_items) {
        return 0;
    }

    $packages = array();

    // If there is more than one item or there is just one item and it requires a container,
    // then check if we should package item(s) in containers.
    if ((count($package_items) > 1) or $package_items[0]['container_required']) {

        // Get all enabled containers.  We get the largest containers first so that later when
        // we loop through each container to see if an item will fit in a container, that check
        // will be more efficient.
        $containers = db_items(
            "SELECT
                id,
                name,
                length,
                width,
                height,
                weight,
                cost
            FROM containers
            WHERE enabled = '1'
            ORDER BY length DESC, width DESC, height DESC");

        // If there is at least one enabled container, then determine if we should use them.
        if ($containers) {

            // Now, we need to figure out which items can fit in at least one container, and which
            // items do not fit in any containers.  We need to figure this out before we use
            // BoxPacker, because BoxPacker will throw an exception and fail if we pass it an item
            // that does not fit in any containers.

            $container_items = array();

            // Loop through each package item in order to determine if it fits in a container.
            foreach ($package_items as $item) {

                // Prepare the item dimensions in order from largest to smallest so we can compare
                // it to the container dimensions.
                $item_dimensions = array($item['length'], $item['width'], $item['height']);
                rsort($item_dimensions);

                $fits = false;

                // Loop through each container to see if this item will fit in a container.
                foreach ($containers as $container) {

                    // Prepare the container dimensions in order from largest to smallest so we can
                    // compare it to the item dimensions.
                    $container_dimensions = array(
                        $container['length'], $container['width'], $container['height']);
                    rsort($container_dimensions);

                    // If this item fits in this container, then remember that and break out of
                    // container loop.
                    if (
                        $item_dimensions[0] <= $container_dimensions[0]
                        and $item_dimensions[1] <= $container_dimensions[1]
                        and $item_dimensions[2] <= $container_dimensions[2]
                    ) {
                        $fits = true;
                        break;
                    }

                }

                // If this item fits in at least one container, then add it as a container item.
                if ($fits) {

                    $container_items[] = $item;

                // Otherwise, this item does not fit in any container, so add package just for it.
                } else {

                    $packages[] = array(
                        'weight' => $item['weight'],
                        'length' => $item['length'],
                        'width' => $item['width'],
                        'height' => $item['height'],
                        'rate_key' => get_rate_key($item),
                        'items' => array($item));

                }

            }

            // If there is at least one item that fits in a container, then use BoxPacker to figure
            // out how the items should be packed in containers.
            if ($container_items) {

                require_once(dirname(__FILE__) . '/boxpacker/init.php');

                $packer = new \DVDoug\BoxPacker\Packer();

                // Add all containers to BoxPacker.
                foreach ($containers as $key => $container) {
                    $packer->addBox(new \DVDoug\BoxPacker\TestBox(
                        $key,
                        $container['width'],
                        $container['length'],
                        $container['height'],
                        $container['weight'],
                        $container['width'],
                        $container['length'],
                        $container['height'],

                        // Use a large max weight, because we don't know the container's max weight.
                        9999999));
                }

                // Add all container items to BoxPacker.
                foreach ($container_items as $key => $item) {
                    $packer->addItem(new \DVDoug\BoxPacker\TestItem(
                        $key,
                        $item['width'],
                        $item['length'],
                        $item['height'],
                        $item['weight'],

                        // Tell BoxPacker that the item must lay flat.  This is the default/normal.
                        // This appears to mean that BoxPacker will only do "2D rotation",
                        // where only the length and width are switched for rotation testing.
                        // BoxPacker has a new feature called "3D rotation" (false)
                        // where there are more orientations (e.g. item is flipped over?).
                        // However, we found that "3D rotation" (false) had bugs, so we stopped
                        // using it.  For example, it incorrectly said 2 items (24x12x18) could
                        // fit in a 28x18x24 container.
                        true));
                }

                // Try to pack the items with BoxPacker.
                try {
                    $boxes = $packer->pack();

                // If there was a BoxPacker error, then log activity and return false, so we don't
                // report an incorrect real-time rate.
                } catch (Exception $e) {
                    log_activity(
                        'Container packer error: ' . $e->getMessage() . "\n" .
                        'Containers: ' . print_r($containers, true) . "\n" .
                        'Items: ' . print_r($container_items, true) . "\n" .
                        'Packer: ' . print_r($packer, true));

                    return false;
                }

                // Loop through the containers that BoxPacker packed, in order to add a package for
                // each.
                foreach ($boxes as $box) {

                    $items = $box->getItems();

                    // If BoxPacker only put one item in this container, then check if we should
                    // not use a container and just package it by itself.
                    if (count($items) == 1) {

                        // Loop through the items object in order to get info for the only item.
                        foreach ($items as $item) {

                            $item = $container_items[$item->getDescription()];

                            // If a container is not required for this item, then don't use the
                            // container and just package the item individually.
                            if (!$item['container_required']) {

                                $packages[] = array(
                                    'weight' => $item['weight'],
                                    'length' => $item['length'],
                                    'width' => $item['width'],
                                    'height' => $item['height'],
                                    'rate_key' => get_rate_key($item),
                                    'items' => array($item));

                                // We are done with this container, so skip to the next container.
                                continue 2;

                            }

                            break;
                            
                        }

                    }

                    // If we got here then that means the container, as BoxPacker packed it, is
                    // good, so let's package it.

                    // We are going to trust BoxPacker and use the combined weight and dimensions
                    // that they give us.
                    $weight = $box->getWeight();
                    $length = $box->getBox()->getOuterLength();
                    $width = $box->getBox()->getOuterWidth();
                    $height = $box->getBox()->getOuterDepth();

                    $package = array(
                        'weight' => $weight,
                        'length' => $length,
                        'width' => $width,
                        'height' => $height,
                        'rate_key' => get_rate_key(array(
                            'weight' => $weight,
                            'length' => $length,
                            'width' => $width,
                            'height' => $height)),
                        'container' => $containers[$box->getBox()->getReference()]);

                    // Let's add info about all the items to the package array, for debugging,
                    // so we know which items are packed in this package.

                    $package['items'] = array();

                    foreach ($items as $item) {
                        $package['items'][] = $container_items[$item->getDescription()];
                    }

                    $packages[] = $package;
                }
            }
        }
    }

    // If there are no packages at this point, then that means that containers were not used,
    // e.g. (maybe because there are no containers) so let's package all the items individually.
    if (!$packages) {

        // Loop through the package items in order to prepare the packages that are required.
        foreach ($package_items as $item) {

            $packages[] = array(
                'weight' => $item['weight'],
                'length' => $item['length'],
                'width' => $item['width'],
                'height' => $item['height'],
                'rate_key' => get_rate_key($item),
                'items' => array($item));
        }
    }

    // Used to store a rate for each unique package property combination
    // so that we don't have to make extra db queries for identical packages.
    $rates = array();

    // Prepare timestamp so we only get cached rates that are recent.
    $day_ago_timestamp = time() - 86400;

    // Used to keep track of the packages, that we don't have cached info for, that we will need
    // to ask the carrier about.
    $pending_packages = array();

    // Loop through the packages in order to get rates for the packages that we have cached
    // rates for, and to determine which are pending packages that we will need to ask the
    // carrier about.
    foreach ($packages as $key => $package) {

        // If we have already dealt with an identical package to this one, then we have already
        // figured out what we need, so skip to the next package.
        if (isset($rates[$package['rate_key']])) {
            continue;
        }

        // Check the cache table for a rate.
        $rate = db_value(
            "SELECT rate
            FROM shipping_rates
            WHERE
                (service = '" . e($service) . "')
                AND (zip_code = '" . e($zip_code) . "')
                AND (weight = '" . e($package['weight']) . "')
                AND (length = '" . e($package['length']) . "')
                AND (width = '" . e($package['width']) . "')
                AND (height = '" . e($package['height']) . "')
                AND (timestamp > '$day_ago_timestamp')");

        // Store the rate in the rates array.  We store a blank rate in the rates array if we
        // do not find a rate, so if there is an identical package in a future loop, then we don't
        // have to check the db again.
        $rates[$package['rate_key']] = $rate;

        // If a rate was not found, then add this package to the pending packages, so we can ask
        // the carrier for the rate.
        if ($rate == '') {
            $pending_packages[] = $package;
        }

    }

    // If there are pending packages, then ask the carrier for the rate.
    if ($pending_packages) {

        if (!function_exists('curl_init')) {
            log_activity(
                'This website cannot communicate with shipping carrier for real-time rates, because
                cURL is not installed. The administrator of this website should install cURL.');
            return false;
        }

        // Figure out the carrier for this service.

        if (mb_substr($service, 0, 4) == 'usps') {
            $carrier = 'usps';

        } else if (mb_substr($service, 0, 3) == 'ups') {
            $carrier = 'ups';
        }

        // Used to store all the new rates that we collect from the carrier for various packages
        // and services.  We will use this array to store the rate info so that we can
        // add info to db after we are done communicating with the carrier.
        $new_rates = array();

        // Get all services from enabled shipping methods, so we know which services we need
        // to deal with.  For example, if there is no enabled shipping method for UPS Ground,
        // then we don't want to hurt performance by dealing with rate for UPS Ground, or fill
        // cache table with unnecessary data.
        $enabled_services = db_values(
            "SELECT DISTINCT(service)
            FROM shipping_methods
            WHERE
                (service != '')
                AND (status = 'enabled')
                AND (start_time <= NOW())
                AND (end_time >= NOW())");

        switch ($carrier) {

            case 'usps':

                if (!USPS_USER_ID) {
                    log_activity(
                        'This website cannot communicate with USPS for real-time rates, because a
                        USPS Web Tools User ID could not be found in the site settings.');
                    return false;
                }

                $request =
                    '<RateV4Request USERID="' . h(USPS_USER_ID) . '">';

                // Get the first 5 digits of the organization zip code, because USPS returns an
                // error if you include +4.
                $organization_zip_code = mb_substr(ORGANIZATION_ZIP_CODE, 0, 5);

                // Loop through the pending packages in order to prepare XML for each.
                foreach ($pending_packages as $key => $package) {

                    // If any dimension is over 12 inches, then USPS considers the package size
                    // to be "LARGE", so prepare data for that.
                    if (
                        ($package['length'] > 12)
                        or ($package['width'] > 12)
                        or ($package['height'] > 12)
                    ) {

                        // The size is large, so USPS requires us to set the container
                        // in that case for some reason.  Otherwise, we just leave it blank.
                        $container = 'RECTANGULAR';

                        $size = 'LARGE';

                    } else {

                        // The size is regular, so we leave the container blank (i.e. "variable"),
                        // because USPS does not allow "RECTANGULAR" for a regular size, for some
                        // reason.
                        $container = '';

                        $size = 'REGULAR';

                    }

                    // Determine if USPS considers the package to be machinable.
                    if (
                        ($package['length'] >= 6)
                        and ($package['length'] <= 34)
                        and ($package['width'] >= 3)
                        and ($package['width'] <= 17)
                        and ($package['height'] >= 0.25)
                        and ($package['height'] <= 17)
                        and ($package['weight'] >= 0.375)
                        and ($package['weight'] <= 35)
                    ) {
                        $machinable = 'true';
                    } else {
                        $machinable = 'false';
                    }

                    // We request rates for all services, and not just the one service that we are
                    // interested in here, in order to minimize carrier API requests.
                    // The rates for the other services will get added to the cache table,
                    // so future requests for the other services will benefit.

                    $request .=
                        '<Package ID="' . $key . '">
                            <Service>All</Service>
                            <ZipOrigination>' . h($organization_zip_code) . '</ZipOrigination>
                            <ZipDestination>' . h($zip_code) . '</ZipDestination>
                            <Pounds>' . h($package['weight']) . '</Pounds>
                            <Ounces>0</Ounces>
                            <Container>' . $container . '</Container>
                            <Size>' . $size . '</Size>
                            <Width>' . h($package['width']) . '</Width>
                            <Length>' . h($package['length']) . '</Length>
                            <Height>' . h($package['height']) . '</Height>
                            <Machinable>' . $machinable . '</Machinable>
                        </Package>';

                }
                    
                $request .= '</RateV4Request>';

                log_activity($request);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,
                    'http://production.shippingapis.com/ShippingAPI.dll?API=RateV4&XML=' .
                    urlencode($request));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                // We had issues in the past where the timeout did not work, when USPS' service
                // went down, and we were only using CURLOPT_TIMEOUT. The request would go on for
                // too long. We are adding CURLOPT_CONNECTTIMEOUT also to attempt to resolve that.
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
                curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
                $response = curl_exec($ch);
                $curl_errno = curl_errno($ch);
                $curl_error = curl_error($ch);
                curl_close($ch);

                log_activity($response);

                // If there was a cURL problem, then log the error and return false.
                if ($curl_errno) {
                    log_activity(
                        'An error occurred while trying to communicate with USPS for real-time rates.' .
                        'cURL Error Number: ' . $curl_errno . '. ' .
                        'cURL Error Message: ' . $curl_error . '.');
                    return false;
                }

                // Check if there is an error.
                preg_match_all('/<error>(.*?)<\/error>/msi', $response, $errors, PREG_SET_ORDER);

                // If there was at least one error, then log errors and return false.
                if ($errors) {

                    $log_error = '';

                    // Loop through the errors so we can add each error to log message.
                    foreach ($errors as $error) {

                        preg_match('/<number>(.*?)<\/number>/msi', $error[1], $match);
                        $number = unhtmlspecialchars($match[1]);
                        
                        preg_match('/<description>(.*?)<\/description>/msi', $error[1], $match);
                        $description = unhtmlspecialchars($match[1]);

                        $log_error .=
                            ' USPS Error Number: ' . $number . '. ' .
                            'USPS Error Message: ' . $description;

                    }

                    log_activity(
                        'An error occurred while trying to communicate with USPS for real-time
                        rates.' . $log_error . ' Request: ' . $request);

                    return false;

                }

                // Loop through the pending packages in order to get the rates out of the XML.
                foreach ($pending_packages as $key => $package) {

                    // Try to find the response for this package in the XMl.
                    preg_match(
                        '/<Package ID="' . $key . '">(.*?)<\/Package>/msi', $response, $package_match);

                    // If USPS did not return info for this package, then something went wrong,
                    // so log and return false.
                    if (!$package_match) {
                        log_activity(
                            'An error occurred while trying to communicate with USPS for real-time
                            rates.  We could not find info for a package in the USPS response. ' .
                            'Request: ' . $request . ' ' .
                            'Response: ' . $response);
                        return false;
                    }

                    // Get all postage containers from response that contain service and rate.
                    preg_match_all(
                        '/<Postage(.*?)>(.*?)<\/Postage>/msi',
                        $package_match[1], $postages, PREG_SET_ORDER);

                    // Loop through all the postages responses, in order to get rate.
                    foreach ($postages as $postage) {

                        // Get the class id so we can figure out the service.
                        preg_match('/CLASSID="(.*?)"/si', $postage[1], $class_id_match);
                        $class_id = $class_id_match[1];

                        // If we could not find the class id for some reason, then skip to the next
                        // postage.
                        if ($class_id == '') {
                            continue;
                        }

                        $rate_service = '';

                        // Get the service from the class id.  We only support some services.
                        switch ($class_id) {
                            
                            case 1:
                                $rate_service = 'usps_priority';
                                break;

                            case 3:
                                $rate_service = 'usps_express';
                                break;

                            case 4:
                                $rate_service = 'usps_ground';
                                break;

                        }

                        // If this is a service that we don't support, or no enabled shipping
                        // methods use this service, then skip to next postage.
                        if (!$rate_service or !in_array($rate_service, $enabled_services)) {
                            continue;
                        }

                        // Get the rate.
                        preg_match('/<Rate>(.*?)<\/Rate>/si', $postage[2], $rate_match);
                        $rate = $rate_match[1];

                        // If we could not find the rate for some reason, then skip to the next
                        // postage.
                        if ($rate == '') {
                            continue;
                        }

                        // Convert to cents.
                        $rate = $rate * 100;

                        // Store the info for this new rate, so later we can add rate to db.
                        $new_rates[] = array(
                            'service' => $rate_service,
                            'weight' => $package['weight'],
                            'length' => $package['length'],
                            'width' => $package['width'],
                            'height' => $package['height'],
                            'rate_key' => $package['rate_key'],
                            'rate' => $rate);
                        
                    }

                }

                break;
            
            case 'ups':

                // Get UPS settings, because we won't have global constants for them, unlike USPS,
                // for performance reasons.
                $config = db_item(
                    "SELECT
                        ups_key,
                        ups_user_id,
                        ups_password,
                        ups_account
                    FROM config");

                if (!$config['ups_key'] or !$config['ups_user_id'] or !$config['ups_password']) {
                    log_activity(
                        'This website cannot communicate with UPS for real-time rates, because a ' .
                        'UPS Access Key, User ID, and/or Password could not be found in the site ' .
                        'settings.');
                    return false;
                }

                // UPS does not support passing multiple packages and showing the negotiated rate
                // for each package, so we are going to send an API request for each package.
                // If you send multiple packages in the same request, then UPS will only show a
                // total negotiated rate for all packages, which creates issues for us when dealing
                // with the cache table and etc.  We need to know the negotiated rate for each
                // package.

                $rate_information = '';
                $shipper_number = '';

                // If there is an account number in the site settings, then enable negotiated rates.
                if ($config['ups_account']) {

                    $rate_information =
                        '<RateInformation>
                            <NegotiatedRatesIndicator/>
                        </RateInformation>';

                    $shipper_number = '<ShipperNumber>' . h($config['ups_account']) . '</ShipperNumber>';

                }

                $state_province_code = '';

                // If there is a state in the site settings, then prepare to pass that to UPS,
                // because UPS might give more accurate info if state is provided.
                if (ORGANIZATION_STATE) {

                    $organization_state = ORGANIZATION_STATE;

                    // If the state in the site settings appears to be the name of the state then
                    // check state table for 2-char state code, because that is what UPS requires.
                    if (mb_strlen($organization_state) > 2) {

                        $organization_state_code = db_value(
                            "SELECT code
                            FROM states
                            WHERE name = '" . e($organization_state) . "'");

                        if ($organization_state_code) {
                            $organization_state = $organization_state_code;
                        }

                    }

                    $state_province_code = '<StateProvinceCode>' . h($organization_state) . '</StateProvinceCode>';

                }

                // We request rates for all services (RequestOption: Shop), and not just the
                // one service that we are interested in here, in order to minimize carrier API
                // requests. The rates for the other services will get added to the cache table,
                // so future requests for the other services will benefit.

                // Prepare the header request info that will be the same for all packages.
                $request_header =
                    '<?xml version="1.0" ?>
                    <AccessRequest xml:lang="en-US">
                        <AccessLicenseNumber>' . h($config['ups_key']) . '</AccessLicenseNumber>
                        <UserId>' . h($config['ups_user_id']) . '</UserId>
                        <Password>' . h($config['ups_password']) . '</Password>
                    </AccessRequest>
                    <?xml version="1.0" ?>
                    <RatingServiceSelectionRequest xml:lang="en-US">
                        <Request>
                            <RequestAction>Rate</RequestAction>
                            <RequestOption>Shop</RequestOption>
                        </Request>
                        <Shipment>
                            ' . $rate_information . '
                            <Shipper>
                                ' . $shipper_number . '
                                <Address>
                                    <PostalCode>' . h(ORGANIZATION_ZIP_CODE) . '</PostalCode>
                                    ' . $state_province_code . '
                                    <CountryCode>US</CountryCode>
                                </Address>
                            </Shipper>
                            <ShipTo>
                                <Address>
                                    <PostalCode>' . h($zip_code) . '</PostalCode>
                                    <StateProvinceCode>' . h($state) . '</StateProvinceCode>
                                    <CountryCode>US</CountryCode>
                                </Address>
                            </ShipTo>';

                $request_footer =
                    '    </Shipment>
                    </RatingServiceSelectionRequest>';

                foreach ($pending_packages as $package) {

                    // UPS returns an error if a dimension or weight is greater than 6 chars,
                    // so let's reduce the length of those values, and round if necessary.

                    // Remove extra zeros on the end.
                    $length = $package['length'] + 0;
                    $width = $package['width'] + 0;
                    $height = $package['height'] + 0;
                    $weight = $package['weight'] + 0;

                    if (strlen($length) > 6) {
                        $length = round($length, 2);
                    }

                    if (strlen($width) > 6) {
                        $width = round($width, 2);
                    }

                    if (strlen($height) > 6) {
                        $height = round($height, 2);
                    }

                    if (strlen($weight) > 6) {
                        $weight = round($weight, 2);
                    }

                    $request =
                        $request_header . '
                        <Package>
                            <PackagingType>
                                <Code>02</Code>
                            </PackagingType>
                            <Dimensions>
                                <UnitOfMeasurement>
                                    <Code>IN</Code>
                                </UnitOfMeasurement>
                                <Length>' . h($length) . '</Length>
                                <Width>' . h($width) . '</Width>
                                <Height>' . h($height) . '</Height>
                            </Dimensions>
                            <PackageWeight>
                                <UnitOfMeasurement>
                                    <Code>LBS</Code>
                                </UnitOfMeasurement>
                                <Weight>' . h($weight) . '</Weight>
                            </PackageWeight>
                        </Package>
                        ' . $request_footer;

                    log_activity($request);

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, 'https://onlinetools.ups.com/ups.app/xml/Rate');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
                    curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    $response = curl_exec($ch);
                    $curl_errno = curl_errno($ch);
                    $curl_error = curl_error($ch);
                    curl_close($ch);

                    log_activity($response);

                    // If there was a cURL problem, then log the error and return false.
                    if ($curl_errno) {
                        log_activity(
                            'An error occurred while trying to communicate with UPS for real-time rates.' .
                            'cURL Error Number: ' . $curl_errno . '. ' .
                            'cURL Error Message: ' . $curl_error . '.');
                        return false;
                    }

                    // Get the response status code in order to determine if there was an error.
                    preg_match('/<ResponseStatusCode>(.*?)<\/ResponseStatusCode>/si', $response, $match);

                    // If there was an error, then log error(s) and return false.
                    if ($match[1] != '1') {

                        // Get all errors.  Normally, it appears there is just one error per
                        // response, however it appears multiple errors can technically appear,
                        // according to the docs, so we check for multiple errors.
                        preg_match_all('/<Error>(.*?)<\/Error>/si', $response, $errors, PREG_SET_ORDER);

                        $log_error = '';

                        // Loop through the errors so we can add each error to log message.
                        foreach ($errors as $error) {

                            preg_match('/<ErrorCode>(.*?)<\/ErrorCode>/si', $error[1], $match);
                            $code = unhtmlspecialchars($match[1]);
                            
                            preg_match('/<ErrorDescription>(.*?)<\/ErrorDescription>/si', $error[1], $match);
                            $description = unhtmlspecialchars($match[1]);

                            $log_error .=
                                ' UPS Error Code: ' . $code . '. ' .
                                'UPS Error Description: ' . $description;

                        }

                        log_activity(
                            'An error occurred while trying to communicate with UPS for real-time
                            rates.' . $log_error . ' Request: ' . $request);

                        return false;

                    }

                    // Get all rated shipments. There is one for each service.
                    preg_match_all('/<RatedShipment>(.*?)<\/RatedShipment>/si', $response,
                        $rated_shipments, PREG_SET_ORDER);

                    // Loop through all rated shipments in order to get rate for each service.
                    foreach ($rated_shipments as $rated_shipment) {

                        // Get service code.
                        preg_match('/<Service>.*?<Code>(.*?)<\/Code>.*?<\/Service>/si',
                            $rated_shipment[1], $match);
                        $service_code = $match[1];

                        $rate_service = '';

                        // Get the service from the service code.  We only support domestic services.
                        switch ($service_code) {
                            
                            case '01':
                                $rate_service = 'ups_next_day_air';
                                break;

                            case '14':
                                $rate_service = 'ups_next_day_air_early';
                                break;

                            case '13':
                                $rate_service = 'ups_next_day_air_saver';
                                break;

                            case '02':
                                $rate_service = 'ups_2nd_day_air';
                                break;

                            case '59':
                                $rate_service = 'ups_2nd_day_air_am';
                                break;

                            case '12':
                                $rate_service = 'ups_3_day_select';
                                break;

                            case '03':
                                $rate_service = 'ups_ground';
                                break;

                        }

                        // If this is a service that we don't support, or no enabled shipping
                        // methods use this service, then skip to next service.
                        if (!$rate_service or !in_array($rate_service, $enabled_services)) {
                            continue;
                        }

                        $rate = '';

                        // If we requested negotiated rates, then look for a negotiated rate first.
                        if ($config['ups_account']) {
                            preg_match('/<NegotiatedRates>.*?<NetSummaryCharges>.*?<GrandTotal>.*?<MonetaryValue>(.*?)<\/MonetaryValue>.*?<\/GrandTotal>.*?<\/NetSummaryCharges>.*?<\/NegotiatedRates>/si',
                                $rated_shipment[1], $match);
                            $rate = $match[1];
                        }

                        // If we did not find a negotiated rate, then look for a standard rate.
                        if ($rate == '') {
                            preg_match('/<TotalCharges>.*?<MonetaryValue>(.*?)<\/MonetaryValue>.*?<\/TotalCharges>/si',
                            $rated_shipment[1], $match);
                            $rate = $match[1];
                        }

                        // If we could not find the rate for some reason, then skip to the next
                        // service.
                        if ($rate == '') {
                            continue;
                        }

                        // Convert to cents.
                        $rate = $rate * 100;

                        // Store the info for this new rate, so later we can add rate to db.
                        $new_rates[] = array(
                            'service' => $rate_service,
                            'weight' => $package['weight'],
                            'length' => $package['length'],
                            'width' => $package['width'],
                            'height' => $package['height'],
                            'rate_key' => $package['rate_key'],
                            'rate' => $rate);

                    }

                }
                
                break;
        }

        // Loop through the new rates that we found, in order to add them to array and db cache
        // table.
        foreach ($new_rates as $rate) {
            
            // If the service for this rate matches the service we are dealing with now,
            // then add the rate to the rates array, so that later we can figure out the rate for
            // each package.
            if ($rate['service'] == $service) {
                $rates[$rate['rate_key']] = $rate['rate'];
            }

            // Delete any old rates in db cache table that might exist.
            db(
                "DELETE FROM shipping_rates
                WHERE
                    (service = '" . e($rate['service']) . "')
                    AND (zip_code = '" . e($zip_code) . "')
                    AND (weight = '" . e($rate['weight']) . "')
                    AND (length = '" . e($rate['length']) . "')
                    AND (width = '" . e($rate['width']) . "')
                    AND (height = '" . e($rate['height']) . "')");

            // Insert new rate into db cache table.
            db(
                "INSERT INTO shipping_rates (
                    service,
                    zip_code,
                    weight,
                    length,
                    width,
                    height,
                    rate,
                    timestamp)
                VALUES (
                    '" . e($rate['service']) . "',
                    '" . e($zip_code) . "',
                    '" . e($rate['weight']) . "',
                    '" . e($rate['length']) . "',
                    '" . e($rate['width']) . "',
                    '" . e($rate['height']) . "',
                    '" . e($rate['rate']) . "',
                    UNIX_TIMESTAMP())");
        }
    }

    $rate = 0;
    $error = false;
    $description = '';

    // Now that we have rates for all packages, loop through packages to calculate total rate.
    foreach ($packages as $key => $package) {

        $package['rate'] = $rates[$package['rate_key']];

        // If we can't find the rate in the rates array, then some issue happened, so mark error.
        // This might happen for example if the service is USPS Ground and the recipient's address
        // does not support Ground (e.g. too close to the origination address).
        if ($package['rate'] == '') {
            $package['error'] = true;
            $error = true;

        } else {

            // If this package is a container and it has a cost, then add cost to the rate.
            if ($package['container']['cost']) {
                $package['rate'] += $package['container']['cost'];
            }

            $rate += $package['rate'];

            // If we are updating a recipient with this real-time rate, rather than just calculating
            // a rate for the list of methods on the shipping method screen, then prepare a
            // description of packages for admin to view on view order screen.

            if ($ship_to_id) {

                if ($description != '') {
                    $description .= ', ';
                }

                // If this package is a container of items, then describe package in a certain way.
                if ($package['container']) {

                    $description .=
                        $package['container']['name'] .
                        ' (' . prepare_amount($package['rate'] / 100) . ', ';

                    foreach ($package['items'] as $item_key => $item) {

                        if ($item_key != 0) {
                            $description .= ', ';
                        }

                        $description .= $item['name'];

                    }

                    $description .= ')';

                // Otherwise this package is just one item, so describe more simply.
                } else {

                    $description .=
                        $package['items'][0]['name'] .
                        ' (' . prepare_amount($package['rate'] / 100) . ')';
                }
            }
        }

        $packages[$key] = $package;
    }

    log_activity(
        'Real-time shipping rate calculation' . "\n" .
        'Total Rate: ' . prepare_amount($rate / 100) . "\n" .
        'Order ID: ' . $_SESSION['ecommerce']['order_id'] . "\n" .
        'Ship To ID: ' . $ship_to_id . "\n" .
        'Service: ' . get_shipping_service_name($service) . "\n" .
        'State: ' . $state . "\n" .
        'Zip Code: ' . $zip_code . "\n" .
        'Error: ' . $error . "\n" .
        'Packages: ' . print_r($packages, true));

    if ($error) {
        return false;
    }

    // If there is a description of packages, then add it to recipient, so admin can view it on
    // view order screen.
    if ($description != '') {
        db(
            "UPDATE ship_tos SET packages = '" . e($description) . "'
            WHERE id = '" . e($ship_to_id) . "'");
    }

    return $rate;

}

// Returns a string with the weight, length, width, and height that can be used for comparions.

function get_rate_key($properties) {

    // We add zero in order to remove extra zeros on the end of the decimal, so that we have issues
    // with "1.10" not matching "1.1".
    return
        ($properties['weight']+0) . '_' .
        ($properties['length']+0) . '_' .
        ($properties['width']+0) . '_' .
        ($properties['height']+0);

}

// Figure out the shipping method base rate for a recipient based on
// whether variable base rate is enabled and the recipient subtotal.
function get_shipping_method_base_rate($properties) {

    $base_rate = $properties['base_rate'];
    $variable_base_rate = $properties['variable_base_rate'];
    $base_rate_2 = $properties['base_rate_2'];
    $base_rate_2_subtotal = $properties['base_rate_2_subtotal'];
    $base_rate_3 = $properties['base_rate_3'];
    $base_rate_3_subtotal = $properties['base_rate_3_subtotal'];
    $base_rate_4 = $properties['base_rate_4'];
    $base_rate_4_subtotal = $properties['base_rate_4_subtotal'];
    $ship_to_id = $properties['ship_to_id'];

    // If variable base rate is disabled for the shipping method,
    // then return the normal base rate.
    if (!$variable_base_rate) {
        return $base_rate;
    }

    $recipient_subtotal = db_value(
        "SELECT SUM(price * CAST(quantity AS signed))
        FROM order_items
        WHERE ship_to_id = '" . e($ship_to_id) . "'");

    // If there is not a 2nd base rate, or if the recipient total is less than
    // the 2nd base rate, then return the base rate.
    if (
        (!$base_rate_2_subtotal)
        || ($recipient_subtotal < $base_rate_2_subtotal)
    ) {
        return $base_rate;

    // Otherwise if there is not a 3rd base rate, or if the recipient total is less than
    // the 3rd base rate, then return the 2nd base rate.
    } else if (
        (!$base_rate_3_subtotal)
        || ($recipient_subtotal < $base_rate_3_subtotal)
    ) {
        return $base_rate_2;

    // Otherwise if there is not a 4th base rate, or if the recipient total is less than
    // the 4th base rate, then return the 3rd base rate.
    } else if (
        (!$base_rate_4_subtotal)
        || ($recipient_subtotal < $base_rate_4_subtotal)
    ) {
        return $base_rate_3;

    // Otherwise return the 4th base rate.
    } else {
        return $base_rate_4;
    }
}