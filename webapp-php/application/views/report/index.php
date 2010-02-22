<?php slot::start('head') ?>
<?php /* Bug#530306 - don't reformat the [@ signature ] part below, it affects
                      our Bugzilla integration. No really. */ ?>
<title><?php if (! empty($report->signature)) { echo '[@ '; out::H($report->signature); echo '] - ';} ?> <?php out::H($report->product) ?> <?php out::H($report->version) ?> Crash Report - Report ID: <?php out::H($report->uuid) ?></title>

    <link rel='alternate' type='application/json' href='<?php echo $reportJsonZUri ?>' />

    <?php echo html::stylesheet(array(
        'css/flora/flora.all.css'
    ), 'screen')?>

    <?php echo html::script(array(
        'js/jquery/plugins/ui/jquery.ui.all.js',
        'js/jquery/plugins/ui/jquery.tablesorter.min.js',
    ))?>

  <script type="text/javascript">//<![CDATA[
      var SocReport = {
          base: '<?= url::site('/correlation/ajax')?>/',
	  path: '<?= $url_path ?>',
	  loading: 'Loading <?= html::image( array('src' => 'img/loading.png', 'width' => '16', 'height' => '17')) ?>'
      };
//]]></script> 

<?php slot::end() ?>
<h1 id="report-header" class="first"><?php out::H($report->product) ?> <?php out::H($report->version) ?> Crash Report <?php
if (! empty($report->signature)) {?>
    [@ <?php out::H($report->signature) ?> ]
<?php }?></h1>
<div id="sumo-link"><?php
if (is_null($report->signature) || empty($report->signature)) { ?>
<a href="http://support.mozilla.com">Visit Mozilla Support for Help</a>
<?php } else { ?>
<a href="http://support.mozilla.com/tiki-newsearch.php?where=all&amp;q=<?=urlencode($report->sumo_signature) ?>" title="Find more answers at support.mozilla.com!">Search Mozilla Support for Help</a>
<?php } ?></div>

<div id="report-header-details">ID: <span><?php out::H($report->uuid) ?></span><br/> Signature: <span><?php out::H($report->{'display_signature'}) ?></span></div>
<div id="report-index" class="flora">

    <ul>
        <li><a href="#details"><span>Details</span></a></li>
        <li><a href="#modules"><span>Modules</span></a></li>
        <li><a href="#rawdump"><span>Raw Dump</span></a></li>
        <li><a href="#extensions"><span>Extensions</span></a></li>
        <li><a href="#comments"><span>Comments</span></a></li>
        <li><a href="#correlation"><span>Correlations</span></a></li>
    </ul>
    <div id="details">
        <table class="list record">
            <tr>
<th>Signature</th><td><?php out::H($report->signature) ?></td>
            </tr>
            <tr>
                <th>UUID</th><td><?php out::H($report->uuid) ?></td>
            </tr>
<?php if (property_exists($report, 'processType')) { ?>
            <tr><th>Process Type</th><td><?= out::H($report->processType) ?>
    <?php if (property_exists($report, 'pluginName')) { ?>
	    <strong class="name"><?= out::H($report->pluginName) ?></strong>
    <?php } ?>
    <?php if (property_exists($report, 'pluginVersion')) { ?>
	    <span>Version:</span><span class="version"><?= out::H($report->pluginVersion) ?></span>
    <?php } ?>
    <?php if (property_exists($report, 'pluginFilename')) { ?>
            <span>Filename:</span> <span class="filename"><?= out::H($report->pluginFilename) ?></span>
    <?php } ?>
                </td></tr>
<?php } ?>
            <tr>
                <th>Time
</th><td><?php out::H($report->date_processed) ?></td>
            </tr>
            <tr>
                <th>Uptime</th><td><?php out::H($report->uptime) ?></td>
            </tr>
            <?php if ($report->last_crash): ?>
            <tr>
                <th>Last Crash</th><td><?php out::H($report->last_crash) ?> seconds before submission</td>
            </tr>
            <?php endif; ?>
            <tr>
                <th>Product</th><td><?php out::H($report->product) ?></td>
            </tr>
            <tr>
                <th>Version</th><td><?php out::H($report->version) ?></td>
            </tr>
            <tr>
                <th>Build ID</th><td><?php out::H($report->build) ?></td>
            </tr>
            <?php if ($branch && !empty($branch->branch)): ?>
            <tr>
                <th>Branch</th><td><?php out::H($branch->branch) ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <th>OS</th><td><?php out::H($report->os_name) ?></td>
            </tr>
            <tr>
                <th>OS Version</th><td><?php out::H($report->os_version) ?></td>
            </tr>
            <tr>
                <th>CPU</th><td><?php out::H($report->cpu_name) ?></td>
            </tr>
            <tr>
                <th>CPU Info</th><td><?php out::H($report->cpu_info) ?></td>
            </tr>
            <tr>
                <th>Crash Reason</th><td><?php out::H($report->reason) ?></td>
            </tr>
            <tr>
                <th>Crash Address</th><td><?php out::H($report->address) ?></td>
            </tr>
<?php if ($logged_in === TRUE) { ?>
	    <tr>
	  <th>Email Address</th><td>
	  <?php if(property_exists($report, 'email') && ! empty($report->email)){?>
               <a href="mailto:<? out::H($report->email) ?>"><? out::H($report->email) ?></a> - Super Sensitive! Don't mess around!
<?php } ?></td>
	    </tr>
            <tr>
	  <th>URL</th><td>
	  <?php if(property_exists($report, 'url') && ! empty($report->url)){?>
               <a href="<? out::H($report->url) ?>"><? out::H($report->url) ?></a> - Super Sensitive! Don't mess around!
<?php } ?></td>
	    </tr>
<?php } ?>

            <tr>
	    <th>User Comments</th><td><?php echo out::H($report->user_comments) ?></td>
            </tr>
<?php if (isset($report->app_notes)) { ?>
            <tr>
	    <th title="Notes added by the application's code during crash">App Notes</th>
            <td><?php echo nl2br( out::H($report->app_notes
, FALSE))  ?></td>
            </tr>
<?php } ?>
<?php if (isset($report->processor_notes)) { ?>
            <tr>
	    <th title="Notes added by Socorro when accepting the crash report">Processor Notes</th>
            <td><?php echo nl2br( out::H($report->processor_notes, FALSE))  ?></td>
            </tr>
<?php } ?>
<?php if (isset($report->distributor)) { ?>
            <tr>
	    <th>Distributor</th><td><?php out::H($report->distributor) ?></td>
            </tr>
<?php } ?>
<?php if (isset($report->distributor_version)) { ?>
            <tr>
	    <th>Distributor Version</th><td><?php out::H($report->distributor_version) ?></td>
            </tr>
<?php } ?>
        </table>
<?php if (array_key_exists($report->signature, $sig2bugs)) { ?>    
      <div id="bugzilla">      
        <h2>Related Bugs</h2>
        <?php View::factory('common/list_bugs', array(
		     'signature' => $report->signature,
                     'bugs' => $sig2bugs[$report->signature],
                     'mode' => 'full',
                     'suppressHeader' => TRUE
	      ))->render(TRUE); ?>
      </div><!-- /bugzilla -->
    <?php }  ?>

      <div id="frames">
    <?php if (isset($report->threads) && count($report->threads)): ?>
           
            <?php function stack_trace($frames) { ?>
                <table class="list">
                    <tr>
                        <th>Frame</th>
                        <th>Module</th>
                        <th class="signature-column">Signature</th>
                        <th>Source</th>
                    </tr>
                    <?php $row = 1 ?>
                    <?php foreach ($frames as $frame): ?>
                        <tr>
                            <td><?php out::H($frame['frame_num']) ?></td>
                            <td><?php out::H($frame['module_name']) ?></td>
                            <td title="<?php out::H($frame['signature']) ?>"><?php out::H($frame['short_signature']) ?></td>
                            <td>
                                <?php if ($frame['source_link']): ?>
                                    <a href="<?php out::H($frame['source_link']) ?>"><?php out::H($frame['source_info']) ?></a>
                                <?php else: ?>
                                    <?php out::H($frame['source_info']) ?>
                                <?php endif ?>
                            </td>
                        </tr>
                        <?php $row += 1 ?>
                    <?php endforeach ?>
                </table>
            <?php } ?>

            <h2>Crashing Thread</h2>
            <?php if (isset($report->threads) && count($report->threads) > $report->crashed_thread ){
                    stack_trace( $report->threads[$report->crashed_thread] );
                  } ?>    
               

            <p id="showallthreads" class="hidden"><a href="#allthreads">Show/hide other threads</a></p>
            <div id="allthreads">
                <?php for ($i=0; $i<count($report->threads); $i++): ?>
                    <?php if ($i == $report->crashed_thread) continue; ?>
                    <h2>Thread <?php out::H($i) ?></h2>
                    <?php stack_trace($report->threads[$i]) ?>
                <?php endfor ?>
            </div>

            <script type="text/javascript">document.getElementById("allthreads").style.display="none";</script>

        <?php endif ?>
      </div><!-- /frames -->
    </div><!-- /details -->


    <div id="modules">
        <?php if (count($report->modules)): ?>
        <table class="list" id="modules-list">
            <tr>
                <th>Filename</th>
                <th>Version</th>
                <th>Debug Identifier</th>
                <th>Debug Filename</th>
            </tr>
            <?php $row = 1 ?>
            <?php foreach ($report->modules as $module): ?>
                <tr>
                    <td><?php out::H($module['filename']) ?></td>
                    <td><?php out::H($module['module_version']) ?></td>
                    <td><?php out::H($module['debug_id']) ?></td>
                    <td><?php out::H($module['debug_filename']) ?></td>
                </tr>
                <?php $row += 1 ?>
            <?php endforeach ?>
        </table>
    <?php endif ?>
    </div><!-- /modules -->


    <div id="rawdump">
        <div class="code"><?php out::H($report->dump) ?></div>

		<?php if ($logged_in && !empty($raw_dump_urls)) { ?>
			<h3>Download the Raw Dump</h3>
			<?php foreach ($raw_dump_urls as $url) { ?>
				<p><a href="<?php out::H($url); ?>"><?php out::H($url); ?></a></p>
			<?php } ?>
		<?php } ?>
    </div><!-- /rawdump -->


    <div id="extensions">
        <?php if (!empty($extensions)) { ?>
	        <table class="list">
	            <tr>
					<th>Extension</th>
	                <th>Extension Id</th>
	                <th>Version</th>
					<th>Current?</th>
	            </tr>
	            <?php $row = 1 ?>
	            <?php foreach ($extensions as $extension) { ?>
	                <tr>
	                    <td><a href="<?php out::H($extension['link']) ?>"><?php out::H($extension['name']) ?></a></td>
	                    <td><?php if (isset($extension['extension_id'])) out::H($extension['extension_id']) ?></td>
	                    <td><?php if (isset($extension['extension_version']))  out::H($extension['extension_version']) ?></td>
						<td><?php 
								if (isset($extension['extension_version']) && isset($extension['latest_version'])) {
									if ($extension['extension_version'] !== $extension['latest_version']) {
							?>
									<strong><?php out::H($extension['latest_version']); ?></strong>
							<?php } else { ?>
									current
								<?php } ?>
							<?php } ?>
						</td>
					<?php $row += 1; ?>
					</tr>
	            <?php } ?>
	        </table>    	
		<?php } else { ?>
			<p><em>No extensions were installed.</em></p>
		<?php } ?>
    </div><!-- /extensions -->
<?php View::factory('common/comments')->render(TRUE); ?>

	<div id="correlation"><h2>Correlations</h2>
	<p>Below are tables of correlation data generated by <a href="http://dbaron.org/mozilla/topcrash-modules">dbaron's scripts</a>. If this crash signature is
a top crasher, then we should be able to load it from <a href="<?= Kohana::config('correlation.path') ?>">the original text files</a>. Only the first <?= Kohana::config('correlation.max_file_size') ?>MB of each Platform's output is loaded.</p>
	<ul>
            <li id="mod"><h3>Modules <a href="#modver" title="Skip Down to Module Versions">Next</a></h3>
	<div id="module_correlation">Loading <?= html::image( array('src' => 'img/loading.png', 'width' => '16', 'height' => '17' )) ?></div></li>

            <li id="modver"><h3>Modules by versions <a href="#add" title="Skip Down to Addons">Next</a>
                <a href="#mod" title="Skip Up to Modules">Previous</a></h3>
                <div id="moduleversion-panel"><button name="moduleversion" class="load-version-data">Load</button></div></li>

            <li id="add"><h3>Addons <a href="#addver" title="Skip Down to Addon Versions">Next</a>
                           <a href="#modver" title="Skip Up to Module Versions">Previous</a></h3>
	<div id="addon_correlation">Loading <?= html::image( array('src' => 'img/loading.png', 'width' => '16', 'height' => '17' )) ?></div></li>

            <li id="addver"><h3>Addons by versions <a href="#cpu" title="Skip Down to CPU Cores">Next</a>
                                       <a href="#add" title="Skip Up to Addons">Previous</a></h3>
                <div id="addonversion-panel"><button name="addonversion" class="load-version-data">Load</button></div></li>

            <li id="cpu"><h3>CPU Cores <a href="#addver" title="Skip Up to Addon Versions">Previous</a></h3>
	<div id="cpu_correlation">Loading <?= html::image( array('src' => 'img/loading.png', 'width' => '16', 'height' => '17' )) ?></div></li>
        </ul>
        </div>

</div> <!-- /report-index -->
    <?php echo html::script(array(
        'js/socorro/report.js'
    ))?>