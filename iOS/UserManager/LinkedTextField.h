//
//  LinkedTextField.h
//  UserManager
//
//  Created by Sebastian Bierman-Lytle on 12/30/14.
//  Copyright (c) 2014 iio interactive. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface LinkedTextField : UITextField

@property(retain, nonatomic)UITextField* nextField;
@property SEL submit;

@end
