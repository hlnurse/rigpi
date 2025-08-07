import serial

def get_rts_state(port='/dev/ttyUSB1'):
	try:
		ser = serial.Serial(port)
		rts_state = ser.rts
		ser.close()
		return rts_state
	except Exception as e:
		return str(e)

if __name__ == "__main__":
	print(get_rts_state())