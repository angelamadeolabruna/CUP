$path = "c:\Users\pablo\UAGRM\SISTEMAS INFORMATICOS 1\ORGANIGRAMA\SISTEMA DEL CUP\DIAGRAMAS\diagrama de analisis de clase de cu\Untitled.mdj"
$jsonStr = Get-Content $path -Raw
# Convert from json
$json = ConvertFrom-Json $jsonStr

$classes = @()

function Find-Classes($obj) {
    if ($obj -is [array]) {
        foreach ($item in $obj) { Find-Classes $item }
    } elseif ($obj -is [System.Management.Automation.PSCustomObject]) {
        if ($obj._type -eq "UMLClass") {
            $stereo = $obj.stereotype
            if ($stereo -is [System.Management.Automation.PSCustomObject]) { $stereo = $stereo.name }
            
            $classInfo = "Class: $($obj.name) | Stereotype: $stereo"
            Write-Host $classInfo
            
            if ($obj.attributes) {
                foreach ($attr in $obj.attributes) { Write-Host "  - Attr: $($attr.name)" }
            }
            if ($obj.operations) {
                foreach ($op in $obj.operations) { Write-Host "  - Oper: $($op.name)" }
            }
            Write-Host "-------------------------"
        }
        foreach ($prop in $obj.psobject.properties) {
            if ($prop.Name -ne "_parent") {
                Find-Classes $prop.Value
            }
        }
    }
}

Find-Classes $json
