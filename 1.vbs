for each n in CreateObject("Scripting.FileSystemObject").Drives
  if n.DriveType = 4 then
    if n.IsReady then 
		Wscript.Echo n.DriveLetter & " is Ready"
	else 
		Wscript.Echo n.DriveLetter & " is not Ready"
  end if
next