<?php
/*******************************************************************************
 * Copyright (c) 2011, 2014 IBM Corporation and Others
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *   IBM Corporation - initial API and implementation
 *******************************************************************************/
use_stylesheet('/opMICExtPlugin/css/stats.css', 'last');
include_component("stats", "barGraphByEvent");
include_component("stats", "activitiesByDate");
//include_component("stats", "streamGraphByUrl");
?>
<script type="text/javascript">
//<![CDATA[
window["__dsl_disable_page_logger"] = true;
//]]>
</script>