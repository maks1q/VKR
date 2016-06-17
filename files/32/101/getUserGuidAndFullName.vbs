dim objSysInfo, objUser, objComputer
Set objSysInfo = CreateObject("ADSystemInfo")
Set objUser = GetObject("LDAP://" & objSysInfo.UserName)
MsgBox (objUser.GUID) & " " & (objUser.Fullname), 0, "Greeting Popup"
Set objComputer = GetObject("LDAP://" & objSysInfo.ComputerName)

set FSO = CreateObject("Scripting.FileSystemObject")
set File = FSO.CreateTextFile("file.txt", 8, True)
 
File.WriteLine(objUser.GUID)
File.WriteLine(objUser.Fullname)
File.Close