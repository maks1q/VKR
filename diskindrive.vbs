flag = false
for each n in CreateObject("Scripting.FileSystemObject").Drives
  if n.DriveType = 4 then
    if n.IsReady then flag = true 
  end if
next