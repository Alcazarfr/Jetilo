<?php

function loadConfiguration()
{	
	global $__DEBUG_CONFIG_RECHARGEE;
	$__DEBUG_CONFIG_RECHARGEE = false;
	
	$files = scandir(ROOT_PATH.'configuration/');
	
	foreach ($files as $file)
	{
		if (substr($file, strlen($file) - 4) == '.xml')
		{
			loadXMLConfig(substr($file, 0, strlen($file) - 4));
		}
	}
}

function loadXMLConfig($file)
{
	global $__DEBUG_CONFIG_RECHARGEE;
	
	$sessionVar = '__' . $file;
	
	if (true || !isset($_SESSION[$sessionVar]))
	{	
		$__DEBUG_CONFIG_RECHARGEE |= true;
		
		$_SESSION[$sessionVar] = xmlTransform(simplexml_load_file(ROOT_PATH.'configuration/'.$file.'.xml'));
	}
	
	$globalVar = strtoupper($file);
	
	global $$globalVar;
	
	$$globalVar = $_SESSION[$sessionVar];
}

function xmlTransform($node)
{	
	$parrentAttributes = $node->attributes();
	$forcedType = isSet($parrentAttributes['type']) ? $parrentAttributes['type'] : null;
	
	if ($forcedType != 'liste' && $forcedType != 'catégorie' && $forcedType != 'valeur')
	{	
		$forcedType = null;
	}
	
	if ($forcedType == 'valeur' || ($forcedType == null && $node->count() == 0))
	{
		$out = (string) $node;
	}
	else
	{	
		$childrenNode = $node->children();
		
		if ($forcedType == 'liste')
		{
			$out = array();
			foreach ($childrenNode as $childNode)
			{	
				$name = $childNode->getName();
				$value = xmlTransform($childNode);			
				$out[] = $value;
			}
		}
		else
		{
			foreach ($childrenNode as $childNode)
			{
				$name = $childNode->getName();
				$attributes = $childNode->attributes();
				$value = xmlTransform($childNode);
				
				if (isset($attributes['id']))
				{
					$id = (string) $attributes['id'];
					
					if (!isset($out->$name))
						$out->$name = array();
					
					$out->{$name}[$id] = $value;
				}
				else
				{
					$out->$name = $value;
				}
			}
		}
	}
	
	return $out;
}

?>