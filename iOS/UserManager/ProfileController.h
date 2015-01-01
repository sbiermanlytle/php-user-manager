//
//  ProfileController.h
//  UserManager
//
//  Created by Sebastian Bierman-Lytle on 12/31/14.
//  Copyright (c) 2014 iio interactive. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "BaseController.h"

@interface ProfileController : BaseController <UITextFieldDelegate, UIScrollViewDelegate>

@property(strong) UIScrollView *scrollView;
@property(strong) LinkedTextField *usernameField;
@property(strong) LinkedTextField *emailField;
@property(strong) LinkedTextField *nameField;
@property(strong) LinkedTextField *passwordField;
@property(strong) LinkedTextField *passwordRetypeField;

@property CGFloat iy;
@property CGFloat ip;
@property CGFloat ih;
@property CGFloat ivp;

-(BOOL)validate_data;
-(BOOL)validate_password;

-(LinkedTextField*) addInputField:(NSString*)title withFrame:(CGRect)frame;

@end
