<?php defined('ABSPATH') || exit; ?>

<div class="mt-2">
	<?php do_action('wordplan_admin_tools_all_before_show'); ?>

	<?php

        foreach($args['object']->tools as $tool_id => $tool_object)
        {
            if(!is_object($tool_object))
            {
	            try
	            {
		            $tool_object = wordplan()->tools()->init($tool_id);
	            }
	            catch(\Wordplan\Exceptions\Exception $e)
	            {
                    continue;
	            }
            }

	        $args =
            [
                'id' => $tool_id,
                'name' => $tool_object->getName(),
                'description' => $tool_object->getDescription(),
                'url' => $args['object']->utilityAdminToolsGetUrl($tool_id),
                'object' => $tool_object,
            ];

            wordplan()->views()->getView('tools/item.php', $args);
        }

    ?>

	<?php do_action('wordplan_admin_tools_all_after_show'); ?>
</div>