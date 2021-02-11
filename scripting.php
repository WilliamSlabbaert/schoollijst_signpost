<?php
include('conn.php');

if ($_GET['type'] == "csv") {

	$synergyids = '';
	$sql = "SELECT IFNULL(GROUP_CONCAT(( SELECT IFNULL(( SELECT GROUP_CONCAT(CONCAT(\"'\",synergyid,\"'\")) FROM orders WHERE imageid = a.id and deleted != 1), '') FROM images2020 a WHERE id = q.imageid AND generiek = '1' )), '') AS synergyids
		FROM orders q
		WHERE synergyid = '" . $_GET['synergyid'] . "' and deleted != 1";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$synergyids = $row['synergyids'];
		}
	} else {
		echo "0 results";
	}

	if($synergyids != ''){
		$sql = "SELECT * FROM labels WHERE synergyid IN (" . $synergyids . ")";
	} elseif(isset($_GET['orderid']) == true){
		$sql = "SELECT * FROM labels q WHERE orderid = '" . $_GET['orderid'] . "'";
	} elseif($_GET['synergyid'] == 'dmo7uTavOb94wStztVMSPkHKwZV4KeCEFKce3t2mMsHpPXG'){
		$sql = "SELECT * FROM labels q WHERE doneby = 'Techdata'";
	} elseif($_GET['synergyid'] == 'Pack3'){
		$sql = "SELECT * FROM labels q WHERE ( SELECT COUNT(*) FROM images2020 WHERE synergyid = q.synergyid AND NAME = 'Pack3' ) != 0";
	} else {
		$sql = "SELECT * FROM labels WHERE synergyid = " . $_GET['synergyid'];
	}
	$result = $conn->query($sql);

	if($_GET['synergyid'] == 'dmo7uTavOb94wStztVMSPkHKwZV4KeCEFKce3t2mMsHpPXG'){
		echo "Label,Serienummer,Order\r\n";
		//echo "SignpostTest,\r\n";
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				echo substr($row["label"], -15) . "," . ltrim($row["serialnumber"], 'S') . ",SP-BYOD20-" . $row["orderid"] . "\r\n";
			}
		} else {
			echo "0results,0results,0results\r\n";
		}
		$conn->close();
	} else {
		echo "Label,Serienummer\r\n";
		//echo "SignpostTest,\r\n";
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				if($row['label'] == '' || $row['label'][0] == '-'){
					echo substr($row["signpost_label"], -15) . "," . ltrim($row["serialnumber"], 'S') . "\r\n";
				} else {
					echo substr($row["label"], -15) . "," . ltrim($row["serialnumber"], 'S') . "\r\n";
				}
			}
		} else {
			echo "0results,0results\r\n";
		}
		$conn->close();
	}

} elseif ($_GET['type'] == "ps1") {

	echo '

# Variables

[Cmdletbinding()]
Param(
	[string]$Computername = "localhost"
)
cls
$PysicalMemory = Get-WmiObject -class "win32_physicalmemory" -namespace "root\CIMV2" -ComputerName $Computername
$CompterInfo = Get-ComputerInfo
#Toevoegen van UI Forms
[void][System.Reflection.Assembly]::LoadWithPartialName("System.Windows.Forms")
############# Computer info VAR #####################
$WinProductName = $CompterInfo.WindowsProductName
$BiosFirmware = $CompterInfo.BiosFirmwareType
$PcProductName = $CompterInfo.CsModel
$OsName = $CompterInfo.OsName
$Processor = $CompterInfo.CsProcessors
$ProcessorName = $Processor.Name
$ProcessorArchitect = $Processor.Architecture
#####################################################
$Databank = "DeviceData"
$Pcsn = (Get-WmiObject -Class Win32_BIOS).SerialNumber
$PCmanufacturer = (Get-WmiObject -Class Win32_ComputerSystem).Manufacturer.Trim()
$bioskey = (Get-WmiObject -Class Softwarelicensingservice).OA3xOriginalProductKey.Trim()
$HardwareHash = (Get-WMIObject -Namespace root/cimv2/mdm/dmmap -Class MDM_DevDetail_Ext01).DeviceHardwareData
# get school id
$SchoolIDPath = "C:\Windows\Setup\Install\BYOD2020-school.txt"

if (Test-Path $SchoolIDPath)
{
$SchoolID = Get-Content -Path $SchoolIDPath
}
else
{
$SchoolID = "niet gevonden"
}
# stop get school id
$global:BYODYear = "BYOD2020"
$global:UserName = "byod-script"
$global:Password = "SignP0st!"
$global:ScriptPath = "C:\Windows\Setup\Install"
$Global:QueryPath = ""
$ScreenProduct = Get-WmiObject -Namespace \'root/WMI\' -Class WMIMonitorID
$ScreenSKU = ""
foreach ($sku in $ScreenProduct){
	$splitSKU = ($SKU.InstanceName).Replace("DISPLAY\", "") -replace \'[^\\]*$\', \'\' -replace "\\", ""
	$ScreenSKU += "$($splitSKU);"
}
$SKU = (Get-WmiObject win32_computersystem).SystemSKUNumber

$Partition = Get-Partition -DriveLetter C
$Disk = Get-Disk $Partition.DiskNumber
$UnallocatedSpace = $Disk.Size - $Disk.AllocatedSize
$DiskFullyUsed = 1

If ($UnallocatedSpace -ge 1073741824)
{
	$DiskFullyUsed = 0
}

$Panel = ""
$PanelResolution = ""
$VerticalReso = (Get-WmiObject -Class Win32_VideoController).CurrentVerticalResolution
$HorizontalReso = (Get-WmiObject -Class Win32_VideoController).CurrentHorizontalResolution
$PanelResolution = "$HorizontalReso x $VerticalReso"

if ($VerticalReso -eq "1080")
{
	$Panel = "FHD"
}
Elseif ($VerticalReso -eq "720")
{
	$Panel = "HD"
}
else
{
	$Panel = "not found"
}

# Hard Disks
function get-Size
{
	Param ($disks)
	$DiskSizesList = ""
	ForEach ($Disk in $Disks){
		$Size = [math]::Round($Disk.Size/1073741824)
		if ($Size -gt 1025 -and $Size -lt 2050){
			return ($Size = 2048)
		}elseif ($Size -gt 900 -and $Size -lt 1025){
			return ($Size = 1024)
		}elseif ($Size -gt 257 -and $Size -lt 513){
			return ($Size = 512)
		}elseif ($Size -gt 130 -and $Size -lt 257){
			return ($Size = 256)
		}elseif ($Size -lt 130){
			return ($Size = 128)
		}
	}
}

$SSD = Get-PhysicalDisk | where MediaType -EQ SSD
$HDD = Get-PhysicalDisk | where MediaType -EQ HDD
$SsdSize = ""
$HddSize = ""

$testhdd = foreach ($t in Get-PhysicalDisk | where MediaType -EQ HDD){
	$HddSize += (get-Size ($t))
	$HddSize += ";"
}

$testssd = foreach ($o in Get-PhysicalDisk | where MediaType -EQ SSD){
	$SsdSize += (get-Size ($o))
	$SsdSize += ";"
}

$HddManufacturer = ""
$SsdManufacturer = ""

$HddManufacturerTest = foreach ($n in $hdd.FriendlyName){
$HddManufacturer += $n
$HddManufacturer += ";"
}

$SsdManufacturerTest = foreach ($b in $SSD.FriendlyName){
$SsdManufacturer += $b
$SsdManufacturer += ";"
}

$TotalHardDisk = (Get-PhysicalDisk).count
# RAM capacititeit
$RamSlots = $($PysicalMemory).Capacity
$Memory = ""
foreach ($Count in $RamSlots)
{
$Memory += $count / 1GB
$Memory += ";"
}

# Totaal RAM geheugen
$TotalMemorys = $((($PysicalMemory).Capacity | Measure-Object -Sum).Sum/1GB)
$TotalMemory =""

foreach ($c in $TotalMemorys)
{
$TotalMemory += $c
}

#snelheid van RAM geheugen
$Speed = ($PysicalMemory).speed
$Clockspeed = ""
foreach ($A in $Speed)
{
$Clockspeed += $A
$Clockspeed += ";"
}

#Fabrikant RAM geheugen
$Merk = ($PysicalMemory).Manufacturer
$RamManufacturer = ""
foreach ($P in $Merk)
{
$RamManufacturer += $P
$RamManufacturer += ";"
}

# aantal ram latjes in het toestel
$UsedSlots = (($PysicalMemory) | Measure-Object).Count

# MacAdressen
$WifiMac = (Get-NetAdapter | select Name,MacAddress | where name -Like "Wi-Fi").MacAddress
$EthernetMac = (Get-NetAdapter | select Name,MacAddress | where name -Like "Ethernet").MacAddress

# Colors Start
$global:BackgroundColor = \'black\'
$global:MessageColor = \'Green\'
$global:ErrorColor = \'Red\'

# Colors Stop
# Functions start

Function Test-computerconnection{ #Controle van de netwerkverbinding
Param(
	[Parameter(Mandatory = $True)]$TestIPName,
	[Parameter(Mandatory = $True)]$TestIP
	)
$Connection = $false
Write-Host "Testing $TestIPName connection ..." -ForegroundColor $MessageColor -BackgroundColor $BackgroundColor
do
{
	If (Test-Connection $TestIP -BufferSize 16 -Count 1 -ea 0 -Quiet)
	{   # Computer can be contacted, return $True
		Write-host "Computer: $TestIP is alive!" -ForegroundColor Yellow -BackgroundColor $BackgroundColor
		$connection = $True
	}
	else
	{
		Write-Host "$TestIPName not responding. Check network connection or settings. Retrying until connection established ...." -ForegroundColor $ErrorColor -BackgroundColor $BackgroundColor
		start-sleep 3
	}
} until ($connection -eq $true)

Write-Host "$TestIPName connection succesfull ...`n" -ForegroundColor $MessageColor -BackgroundColor $BackgroundColor
}

# Functions Stop
# Start Controle netwerkverbinding

$GateWayIP = Get-NetRoute | where {$_.DestinationPrefix -eq \'0.0.0.0/0\'} | select { $_.NextHop }
$GateWayIP = $GateWayIP.\' $_.NextHop \'
Test-computerconnection Gateway $GateWayIP
Test-computerconnection Internet 8.8.8.8

$DNSHostName = $env:COMPUTERNAME
$wpa = Get-WmiObject SoftwareLicensingProduct -ComputerName $DNSHostName -Filter "ApplicationID = \'55c92734-d682-4d71-983e-d6ec3f16059f\'" -Property LicenseStatus -ErrorAction Stop
$result = "noidea"
:outer foreach($item in $wpa) {
	switch ($item.LicenseStatus) {
		0 {$result = "Unlicensed"}
		1 {$result = "Licensed"; break outer}
		2 {$result = "Out-Of-Box Grace Period"; break outer}
		3 {$result = "Out-Of-Tolerance Grace Period"; break outer}
		4 {$result = "Non-Genuine Grace Period"; break outer}
		5 {$result = "Notification"; break outer}
		6 {$result = "Extended Grace"; break outer}
		default {$result = "Unknown value"}
	}
}
if ($result -ne "Licensed") {
	$WindowsKey = (Get-WmiObject -query \'select * from SoftwareLicensingService\').OA3xOriginalProductKey
	Start-Process -filepath C:\Windows\System32\changePK.exe -ArgumentList "/ProductKey $($WindowsKey)"
}

#$AutopilotTest = (C:\Windows\Setup\Install\MySQL.ps1 -Query "SELECT sn From autopilot where sn = \'$sn\'").sn

#if ($AutopilotTest -ne $sn)
#{
try
{
	C:\Windows\Setup\Install\MySQL.ps1 -Query "insert into $Databank (SynergyID,SerieNummer,HardWareHash,PanelResolutie,Panel,PanelSKU,BiosVersion,BiosWinKey,MacWifi,MacEthernet,RamUsedSlots,RamMemory,RamTotalMemory,RamManufacturer,RamClockspeed,PcManufacturer,PcSKU,PcModel,PcProcessor,PcProcessorArchitect,OsName,StorageTotalDisks,hddSize,ssdSize,ssdManufacturer,hddManufacturer,DiskFullUsed) VALUES (\'$SchoolID\',\'$pcsn\',\'$HardwareHash\',\'$PanelResolution\',\'$Panel\',\'$ScreenSKU\',\'$BiosFirmware\',\'$bioskey\',\'$WifiMac\',\'$EthernetMac\',\'$UsedSlots\',\'$Memory\',\'$TotalMemory\',\'$RamManufacturer\',\'$Clockspeed\',\'$PCmanufacturer\',\'$sku\',\'$PcProductName\',\'$ProcessorName\',\'$ProcessorArchitect\',\'$OsName\',\'$TotalHardDisk\',\'$HddSize\',\'$SsdSize\',\'$SsdManufacturer\',\'$HddManufacturer\',\'$DiskFullyUsed\')"
}
catch
{
	break
}
#}

# Start rename pc

try
{
	$Hostname = (C:\Windows\Setup\Install\MySQL.ps1 -Query "SELECT Label FROM $Databank WHERE SerieNummer =  \'$Pcsn\'").label

	if (!$Hostname)
	{
		Rename-Computer -NewName $Pcsn
	}
	else
	{
		Rename-Computer -NewName $Hostname
	}
}
catch
{
	break
}
# end rename pc

Remove-Item -Path C:\Windows\Setup\Install\* -include *.xml
Remove-Item -Path C:\Windows\Setup\Install\* -include *.ps1
Remove-Item -Path C:\Windows\Setup\Install\* -include *.bat
Remove-Item -Path C:\Windows\Setup\Install\* -include *.lnk
Remove-Item -Path C:\Windows\Setup\Install\* -include *.cmd
Remove-Item -Path C:\Windows\Setup\Install\* -include *.txt
Remove-Item -Path C:\Windows\Setup\Install\* -include *.exe

##### test zone variable #####
# $HDD = ""
# $HDDSize = ""
# FHD = 1080
#  HD = 720
# $hostname = ""

	';

} elseif ($_GET['type'] == "mysql") {

echo '

Param(
[Parameter(
Mandatory = $true,
ParameterSetName = \'\',
ValueFromPipeline = $true)]
[string]$Query
)
$MySQLAdminUserName = \'autopilot-jelle\'
$MySQLAdminPassword = \'77oILLP85cRT\'
$MySQLDatabase = \'byod-orders\'
$MySQLHost = \'newserver.signpost.be\'
$ConnectionString = "server=" + $MySQLHost + ";port=3306;uid=" + $MySQLAdminUserName + ";pwd=" + $MySQLAdminPassword + ";database="+$MySQLDatabase
Try {
[void][System.Reflection.Assembly]::LoadWithPartialName("MySql.Data")
$Connection = New-Object MySql.Data.MySqlClient.MySqlConnection
$Connection.ConnectionString = $ConnectionString
$Connection.Open()
$Command = New-Object MySql.Data.MySqlClient.MySqlCommand($Query, $Connection)
$DataAdapter = New-Object MySql.Data.MySqlClient.MySqlDataAdapter($Command)
$DataSet = New-Object System.Data.DataSet
$RecordCount = $dataAdapter.Fill($dataSet, "data")
$DataSet.Tables[0]
}
Catch {
Write-Host "ERROR : Unable to run query : $query `n$Error[0]"
}
Finally {
$Connection.Close()
}

';

}

?>
