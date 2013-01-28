<?php

	// The final stage of setting the configuration.inc.php to its place. First of all, let us get the variables
	// THe directories

	// Before anything, we have to see that the values were recieved.
	// Set Error to null
	$strError = null;
	if(!isset($_POST['docroot'])) {
		$strError = 'This file can be accessed only while you follow the configuration wizard step by step. Please go back to <a href="step_1.php">Step 1</a> to start over.';
	}
	$strDocroot = $_POST['docroot'];
	$strVirtDir = $_POST['virtdir'];
	$strSubDir = $_POST['subdir'];
	$strConfigSubPath =  DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'configuration';

	// let us see if there already is a configuration.inc.php file or not.
	$blnConfigFileExists = false;
	if(file_exists($strDocroot . $strVirtDir . $strSubDir . $strConfigSubPath . '/configuration.inc.php')) {
		$blnConfigFileExists = true;
	}

	// Database values
	$strDB_Adapter = $_POST['db_server_adapter'];
	$strDB_ServerAddress = $_POST['db_server_address'];
	$strDB_Port = $_POST['db_server_port'];
	$strDB_DbName = $_POST['db_server_dbname'];
	$strDB_Username = $_POST['db_server_username'];
	$strDB_Password = $_POST['db_server_password'];
	// Now read the text from the configuration.inc.php.sample file
	$strConfigSampleText = file_get_contents($strDocroot . $strVirtDir . $strSubDir . $strConfigSubPath . '/configuration.inc.php.sample');

	if($strConfigSampleText === false) {
		if($strError == null) {
			$strError = "The sample configuration file is missing. It is needed to generate the final config file.";
		}
	}

	// We now have the sample config file. Time to replace the strings.
	$strConfigText = $strConfigSampleText;
	$strConfigText = str_replace('{C:/xampp/xampp/htdocs}', $strDocroot, $strConfigText);
	$strConfigText = str_replace('{~my_user}', $strVirtDir, $strConfigText);
	$strConfigText = str_replace('{/qcubed2}', $strSubDir, $strConfigText);
	$strConfigText = str_replace('{db1_adapter}', $strDB_Adapter, $strConfigText);
	$strConfigText = str_replace('{db1_serverAddress}', $strDB_ServerAddress, $strConfigText);
	// if the port was left blank, then we replace it with null
	if(trim($strDB_Port) == '') {
		// blank value.set as null
		$strDB_Port = 'null';
	}
	$strConfigText = str_replace('{db1_serverport}', $strDB_Port, $strConfigText);
	$strConfigText = str_replace('{db1_dbname}', $strDB_DbName, $strConfigText);
	$strConfigText = str_replace('{db1_username}', $strDB_Username, $strConfigText);
	$strConfigText = str_replace('{db1_password}', $strDB_Password, $strConfigText);

	$strConfigText_Final = $strConfigText;

	// Now the final text should be ready.
	// Display the final text
//	echo '<html><body><textarea cols="160" rows="30"> '. $strConfigText . ' </textarea></body>';
//	exit();

	// We will start the HTML output now.
?>

<html>
<head>
    <title>QCubed Installation Wizard - Step 3</title>
    <style type="text/css">
        label {
            font-weight: bold;
        }

        div.helptext {
            border: 1px solid #444444;
            background: #EEEEEE;
            padding: 10px;;
        }
    </style>
</head>
<body>
<div style="display: block; font-family: Arial, Sans-Serif;">
	<div style="display: block; margin-left: auto; margin-right: auto; width: 800px; background: #FFDDDD; padding: 10px; border: 1px solid #DD0000">
		<h1>
			QCubed Installation Wizard
		</h1>

        <h2 style="color: #AA3333">Step 3: Save the configuration.inc.php file</h2>

		<?php

		if($strError != null) {
			// There was error. Display it
			echo '
				<div style="color: #DD3333">
					<strong>Error:</strong>' . $strError . '
				</div>';
		} else {
			// No errors till now.
			// File creation status indicator
			$strFileCreationStatus = 'unknown';
			// Is there a configuration.inc.php file already?
			if($blnConfigFileExists) {
				// it is already there.
				echo 'There already is a <code>configuration.inc.php</code> file located at ' . $strDocroot . $strVirtDir . $strSubDir . $strConfigSubPath . '. The existing file will not be overwritten. However, the text generated by the wizard is available to you, if you want to use it manually.';
				$strFileCreationStatus = 'exists';
			} else {
				// The configuration file is not there. Try to create one.
				// use the @ to prevent exception traces for special debug serever configurations, like 
				$rscFileHandle = @fopen($strDocroot . $strVirtDir . $strSubDir . $strConfigSubPath . '/configuration.inc.php', 'w');
				if($rscFileHandle === false) {
					// File creation failed.
					echo '
						<div style="color: #DD3333">
							<strong>Error:</strong> File creation failed. It is possible that the wizard does not have the permission to create a file in ' .
							'<code>' .
								$strDocroot . $strVirtDir . $strSubDir . $strConfigSubPath .
							'</code>.' .
							' The generated text is available here. You can create the configuration file (filename should be <code>configuration.inc.php</code>) manually in the directory: ' .
							'<code>' . $strDocroot . $strVirtDir . $strSubDir . $strConfigSubPath . '</code>' .
							' and put the generated contents into it.
						</div>
					';
					$strFileCreationStatus = 'creation_failed';
				} else {
					// File created. Now we will write the data into it.
					fwrite($rscFileHandle, $strConfigText_Final);
					// close the handle
					fclose($rscFileHandle);
					// Tell the user that the file was created.
					echo 'The configuration file has been generated with the contents below.';
					$strFileCreationStatus = 'created';
				}
			}

			// Show the contents of the file and disable editing
			echo '<textarea name="final_config" disabled="disabled" rows="20" cols="100">' . $strConfigText_Final . '</textarea>';
			// depending on the file creation status, show the message at the bottom.
			switch ($strFileCreationStatus) {
				case 'exists':
				case 'creation_failed':
					echo '<br/> <a href="' . $strVirtDir . $strSubDir . '/assets/_core/php/_devtools/config_checker.php">Launch the config checker</a>';
					break;
				case 'created':
					?>
			<br/><br/>
			<div class="helptext">
				Configuration file was created!<br/>
				Make sure to revert directory permissions back for security:<br/>
				<code>chmod 775 <?php echo $strDocroot . $strSubDir . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'configuration' ; ?></code>
			</div>
			<p></p>
			<?php
					echo '<a href="' . $strVirtDir . $strSubDir . '/assets/_core/php/_devtools/config_checker.php">Launch the config checker</a> to make sure everything went fine.';
					break;
				default:
					// do nothing
					break;
			}
		}

	?>

</body>
</html>
