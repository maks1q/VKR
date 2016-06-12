var objSysInfo, objUser, objComputer;
objSysInfo = Object.create("ADSystemInfo");
objUser = GetObject("LDAP://" & objSysInfo.UserName);
objComputer = GetObject("LDAP://" & objSysInfo.ComputerName);
WScript.Echo((objUser.GUID) & " " & (objUser.Fullname), 0, "Greeting Popup");