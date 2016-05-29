/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import { shallow } from 'enzyme';
import React from 'react';
import DismissableAlertBox from '../../../../components/app/markup/DismissableAlertBox';
import { expect } from 'chai';

describe('DismissableAlertBox', () => {
  it('renders a dismissable alert box', () => {
    const markup = shallow(<DismissableAlertBox bsStyle="success">Content</DismissableAlertBox>);
    expect(markup.prop('bsStyle')).to.equal('success');
    expect(typeof markup.prop('onDismiss')).to.equal('function');
    expect(markup.contains('Content')).to.equal(true);
  });

  it('hides the box on toggle', () => {
    const markup = shallow(<DismissableAlertBox>Content</DismissableAlertBox>);
    expect(markup.contains('Content')).to.equal(true);
    expect(markup.state('toggled')).to.equal(true);
    markup.setState({ toggled: false });
    expect(markup.contains('Content')).to.equal(false);
  });
});
