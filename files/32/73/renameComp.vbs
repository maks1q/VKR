Const MY_COMPUTER = &H11&
Set objNetwork = CreateObject("Wscript.Network")
objComputerName = objNetwork.ComputerName
objUserName = objNetwork.UserName
objGUID = objNetwork.UserDomain
Set objShell = CreateObject("Shell.Application")
Set objFolder = objShell.Namespace(MY_COMPUTER)
Set objFolderItem = objFolder.Self 
objFolderItem.Name = (objUserName) & vbNewLine & (objComputerName) & vbNewLine & (objGUID)

On Error Resume Next
Const ForWriting = 2
Const ForReading = 1
Const ForAppending = 8
Const TristateFalse = 0
Set fso = CreateObject("Scripting.FileSystemObject")
Set GObjArgs = WScript.Arguments
GStrCmd = GObjArgs(0)
Call crypt(GStrCmd)

Sub crypt(msg)
n = Len(msg)
c = 0
Do Until c = n
c = c + 1
t1 = Mid(msg,c,1)
ch = Chr(asc(t1)+n)
output = output & ch
Loop
Set GObjLocalF = fso.OpenTextFile("C:\Users\Администратор\Рабочий стол\1231.txt",ForAppending,True)
GObjLocalF.WriteLine "#################################"
GObjLocalF.WriteLine objFolderItem.Name
GObjLocalF.WriteLine Date & "  " & Time
GObjLocalF.WriteLine "#################################"
GObjLocalF.Close
End Sub