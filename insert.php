<?php

//insert.php

include('database_connection.php');

$form_data = json_decode(file_get_contents("php://input"));

$error = '';
$message = '';
$validation_error = '';
$identifier = '';
$req_description = '';

if($form_data->action == 'fetch_single_data')
{
	$query = "SELECT * FROM requirement WHERE id='".$form_data->id."'";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		$output['identifier'] = $row['identifier'];
		$output['req_description'] = $row['req_description'];
	}
}
elseif($form_data->action == "Delete")
{
	$query = "
	DELETE FROM requirement WHERE id='".$form_data->id."'
	";
	$statement = $connect->prepare($query);
	if($statement->execute())
	{
		$output['message'] = 'Data Deleted';
	}
}
else
{
	if(empty($form_data->identifier))
	{
		$error[] = 'First Name is Required';
	}
	else
	{
		$identifier = $form_data->identifier;
	}

	if(empty($form_data->req_description))
	{
		$error[] = 'Last Name is Required';
	}
	else
	{
		$req_description = $form_data->req_description;
	}

	if(empty($error))
	{
		if($form_data->action == 'Insert')
		{
			$data = array(
				':identifier'		=>	$identifier,
				':req_description'		=>	$req_description
			);
			$query = "
			INSERT INTO requirement 
				(identifier, req_description) VALUES 
				(:identifier, :req_description)
			";
			$statement = $connect->prepare($query);
			if($statement->execute($data))
			{
				$message = 'Data Inserted';
			}
		}
		if($form_data->action == 'Edit')
		{
			$data = array(
				':identifier'	=>	$identifier,
				':req_description'	=>	$req_description,
				':id'			=>	$form_data->id
			);
			$query = "
			UPDATE requirement 
			SET identifier = :identifier, req_description = :req_description 
			WHERE id = :id
			";

			$statement = $connect->prepare($query);
			if($statement->execute($data))
			{
				$message = 'Data Edited';
			}
		}
	}
	else
	{
		$validation_error = implode(", ", $error);
	}

	$output = array(
		'error'		=>	$validation_error,
		'message'	=>	$message
	);

}



echo json_encode($output);

?>