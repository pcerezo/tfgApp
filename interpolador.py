#!/usr/bin/env python
import sys
import numpy as np
import scipy.interpolate as interp
import matplotlib.pyplot as plt
from mpl_toolkits.mplot3d import Axes3D
import math
import matplotlib.colors as mcolors

tIR = []
tSensor = []
mag = []
hz = []
alt = []
azi = []


nombreFich = sys.argv[1]
destino = sys.argv[2]
interpolador = "0"

if (len(sys.argv) > 3):
    interpolador = sys.argv[3]

f = open(nombreFich, "r")

fileContents = f.read() 

lines = fileContents.split("\n")

lines = lines[1:]  # Quitamos cabecera

for l in lines:
    campos = l.split("\t")
    
    count = 0
    
    for c in campos:
        if count == 3:
            c = c.strip()
            tIR.append(float(c))
        elif count == 4:
            c = c.strip()
            tSensor.append(float(c))
        elif count == 5:
            c = c.strip()
            mag.append(float(c))
        elif count == 6:
            c = c.strip()
            hz.append(float(c))
        elif count == 7:
            c = c.strip()
            alt.append(float(c))
        elif count == 8:
            c = c.strip()
            azi.append(float(c))
        
        
        count += 1

#mag[0] = 23
x = [None] * len(alt)
y = [None] * len(alt)
z = [None] * len(alt)
points = [None] * len(alt)

for i in range(0, len(alt)):
  #  print(str(alt[i]) + " - " + str(azi[i]))

    x[i] = math.cos((azi[i] - 90) * math.pi * 2 / 360.0) * (90 - alt[i])
    y[i] = math.sin((azi[i] - 90) * math.pi * 2 / 360.0) * (90 - alt[i])
    z[i] = mag[i]
    points[i] = [x[i], y[i]]


if (interpolador == '1'):
    interpolator = interp.CloughTocher2DInterpolator(np.array([x,y]).T, z)
else:
#interpolator = interp.LinearNDInterpolator(np.array([x,y]).T, z)

    interpolator = interp.NearestNDInterpolator(np.array(points), np.array(z))
#interpolator = interp.interp2d(x,y,z)

#print(y)



# go linearly in the x grid
xline = np.linspace(min(x), max(x), 1000)
# go logarithmically in the y grid (considering y distribution)
yline = np.linspace(min(y), max(y), 1000)
# construct 2d grid from these
xgrid,ygrid = np.meshgrid(xline, yline)
# interpolate z data; same shape as xgrid and ygrid
z_interp = interpolator(xgrid, ygrid)

#print(z_interp)

# create 3d Axes and plot surface and base points
#fig = plt.figure()
#ax = fig.add_subplot(111, projection='3d')
#ax.plot_surface(xgrid, ygrid, z_interp, cmap='viridis', vmin=min(z), vmax=max(z))
#ax.plot(x, y, z, 'ro')
#ax.set_xlabel('x')
#ax.set_ylabel('y')
#ax.set_zlabel('z')

cmap = mcolors.LinearSegmentedColormap.from_list("n", [(0.0, (248.0/255.0,251.0/255.0,195.0/255.0)), (0.15, (225.0/255.0,242.0/255.0,175.0/255.0)), (0.5, (53.0/255.0,173.0/255.0,194.0/255.0)), (1, (21.0/255.0,43.0/255.0,115.0/255.0))])


#plt.imshow(z_interp, cmap=cmap, interpolation='none', vmin = 19.1, vmax=21.1) # cmap='viridis_r'
#plt.show()
plt.imsave(sys.argv[2], z_interp, cmap=cmap, vmin = 19.1, vmax=21.1)
