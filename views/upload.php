<?php

// �������, � ������� �� ����� ��������� ����:
$uploaddir = './files/';
$uploadfile = $uploaddir.basename($_FILES['uploadfile']['name']);

// �������� ���� �� �������� ��� ���������� �������� ������:
if (copy($_FILES['uploadfile']['tmp_name'], $uploadfile))
{
echo "<h3>���� ������� �������� �� ������</h3>";
}
else { echo "<h3>������! �� ������� ��������� ���� �� ������!</h3>"; exit; }

// ������� ���������� � ����������� �����:
echo "<h3>���������� � ����������� �� ������ �����: </h3>";
echo "<p><b>������������ ��� ������������ �����: ".$_FILES['uploadfile']['name']."</b></p>";
echo "<p><b>Mime-��� ������������ �����: ".$_FILES['uploadfile']['type']."</b></p>";
echo "<p><b>������ ������������ ����� � ������: ".$_FILES['uploadfile']['size']."</b></p>";
echo "<p><b>��������� ��� �����: ".$_FILES['uploadfile']['tmp_name']."</b></p>";

?>