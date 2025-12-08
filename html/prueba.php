<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
.circular {
  --porcentaje: 6; /* Cambia el valor (0 a 100) */
  --color: #4caf50;

  width: 120px;
  height: 120px;
  border-radius: 50%;
  background:
    conic-gradient(
      var(--color) calc(var(--porcentaje) * 1%),
      #ddd 0
    );
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 22px;
  font-weight: bold;
  color: #333;
}

.circular::before {
  content: "";
  position: absolute;
  width: 90px;
  height: 90px;
  background: #fff;
  border-radius: 50%;
}
</style>
</head>
<body>

<div class="circular"></div>

</body>
</html>
