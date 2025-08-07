#!/usr/bin/env python
import serial

ser = serial.Serial('/dev/ttyUSB0')
#ser.close()
#ser.setRTS(False)  # Set RTS to False
#ser.open()
rts_state = ser.rts
print(rts_state)
if rts_state==False:	
	print(0)
else:
	print(1)
