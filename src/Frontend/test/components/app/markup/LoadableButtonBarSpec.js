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

import LoadableButtonBar from '../../../../components/app/markup/LoadableButtonBar';
import React from 'react';
import { expect } from 'chai';
import { shallow } from 'enzyme';

describe('LoadableButtonBar', () => {
  it('renders a button bar', () => {
    const markup = shallow(<LoadableButtonBar progress={false} btnLabel="Label" />);
    expect(markup.hasClass('form-group'));

    const btn = markup.find('button');
    expect(btn.prop('type')).to.equal('submit');
    expect(btn.hasClass('btn btn-primary spinner-btn')).to.equal(true);
    expect(btn.contains('Label')).to.equal(true);
  });

  it('changes the progress state', () => {
    const markup = shallow(<LoadableButtonBar progress={false} btnLabel="Label" />);
    expect(markup.find('button').prop('disabled')).to.not.equal(true);

    markup.setProps({ progress: true });
    expect(markup.find('button').prop('disabled')).to.equal('disabled');
  });
});
